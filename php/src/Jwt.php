<?php
/**
 * JWT minimal en PHP puro.
 *
 *  - Solo soporta `HS256` (HMAC-SHA256). Cualquier otro `alg` (incluido
 *    `none`) se rechaza explicitamente para evitar alg-confusion attacks.
 *  - Comparacion de firmas con `hash_equals` (timing-safe).
 *  - Cookies con `HttpOnly + SameSite=Strict + Secure si HTTPS`.
 *  - El secreto y los TTL vienen de variables de entorno (`JWT_SECRET`,
 *    `JWT_ACCESS_TTL`, `JWT_REFRESH_TTL`) o de
 *    `php/config/config.php['jwt']`.
 *
 * Integrada con `Auth` (login/logout emiten/limpian cookies, `start()`
 * rehidrata la sesion desde el access token si existe).
 */

require_once __DIR__ . '/BE/BEUser.php';

class Jwt
{
    public const ACCESS_COOKIE  = 'dpa_jwt';
    public const REFRESH_COOKIE = 'dpa_jwt_r';

    private const ALG = 'HS256';

    /** @var array<string,mixed>|null */
    private static ?array $cfgCache = null;

    /**
     * Resuelve la configuracion en este orden de prioridad (mas alto gana):
     *   1. Variables de entorno (`JWT_SECRET`, `JWT_ISSUER`, ...)
     *   2. `php/config/config.php['jwt']`
     *   3. Defaults hardcodeados.
     *
     * Las env vars se aplican como override **solo si estan definidas**
     * (`getenv() !== false`). Esto permite usar valores legitimos como
     * `JWT_LEEWAY=0` (que con el operador `?:` se confundiria con "no
     * seteado" y caeria al default).
     *
     * @return array{secret:string,issuer:string,audience:string,access_ttl:int,refresh_ttl:int,leeway:int}
     */
    private static function cfg(): array
    {
        if (self::$cfgCache !== null) {
            return self::$cfgCache;
        }

        // 3. Defaults
        $cfg = [
            'secret'      => '',
            'issuer'      => 'datpos-admin',
            'audience'    => 'datpos-admin-web',
            'access_ttl'  => 3600,                  // 1h
            'refresh_ttl' => 60 * 60 * 24 * 7,      // 7d
            'leeway'      => 30,                    // 30s clock skew
        ];

        // 2. Config file (override de defaults)
        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            $appCfg = require $configFile;
            if (is_array($appCfg) && isset($appCfg['jwt']) && is_array($appCfg['jwt'])) {
                $cfg = array_merge($cfg, $appCfg['jwt']);
            }
        }

        // 1. Env vars (override de config + defaults). `getenv()` devuelve
        // false si la var no esta definida; cualquier valor explicito (incluso
        // string "0") se respeta.
        $envMap = [
            'secret'      => 'JWT_SECRET',
            'issuer'      => 'JWT_ISSUER',
            'audience'    => 'JWT_AUDIENCE',
            'access_ttl'  => 'JWT_ACCESS_TTL',
            'refresh_ttl' => 'JWT_REFRESH_TTL',
            'leeway'      => 'JWT_LEEWAY',
        ];
        foreach ($envMap as $key => $envName) {
            $val = getenv($envName);
            if ($val !== false) {
                $cfg[$key] = $val;
            }
        }

        // Cast a los tipos esperados (los valores podrian venir como string
        // desde env vars).
        $cfg['secret']      = (string)$cfg['secret'];
        $cfg['issuer']      = (string)$cfg['issuer'];
        $cfg['audience']    = (string)$cfg['audience'];
        $cfg['access_ttl']  = (int)$cfg['access_ttl'];
        $cfg['refresh_ttl'] = (int)$cfg['refresh_ttl'];
        $cfg['leeway']      = (int)$cfg['leeway'];

        if ($cfg['secret'] === '') {
            throw new RuntimeException(
                "JWT_SECRET no configurado. Definir env var JWT_SECRET o " .
                "agregar 'jwt' => ['secret' => '<aleatorio>'] en php/config/config.php"
            );
        }

        return self::$cfgCache = $cfg;
    }

    /**
     * Genera un access token (TTL corto) con los datos del usuario necesarios
     * para rehidratar la sesion sin tocar la BD.
     */
    public static function issueAccess(BEUser $u): string
    {
        $c = self::cfg();
        $now = time();
        return self::encode([
            'iss'  => $c['issuer'],
            'aud'  => $c['audience'],
            'iat'  => $now,
            'exp'  => $now + (int)$c['access_ttl'],
            'typ'  => 'access',
            'sub'  => $u->ccod_usuario,
            'uid'  => $u->id_usuario,
            'rol'  => $u->id_rol,
            'emp'  => $u->ccod_empresa,
            'dsc'  => $u->cdsc_usuario,
            'edsc' => $u->cdsc_empresa,
            'bd'   => $u->cnombre_bd,
            'srv'  => $u->cnombre_servidor,
        ], $c['secret']);
    }

    /**
     * Reconstruye un BEUser a partir de los claims de un access token.
     *
     * @param array<string,mixed> $claims
     */
    public static function userFromClaims(array $claims): BEUser
    {
        $u = new BEUser();
        $u->id_usuario       = (int)($claims['uid']  ?? 0);
        $u->ccod_usuario     = (string)($claims['sub']  ?? '');
        $u->cdsc_usuario     = (string)($claims['dsc']  ?? '');
        $u->id_rol           = (string)($claims['rol']  ?? '');
        $u->ccod_empresa     = (string)($claims['emp']  ?? '');
        $u->cdsc_empresa     = (string)($claims['edsc'] ?? '');
        $u->cnombre_bd       = (string)($claims['bd']   ?? '');
        $u->cnombre_servidor = (string)($claims['srv']  ?? '');
        return $u;
    }

    /**
     * Genera un refresh token (TTL largo) que solo lleva el sub.
     */
    public static function issueRefresh(BEUser $u): string
    {
        $c = self::cfg();
        $now = time();
        return self::encode([
            'iss' => $c['issuer'],
            'aud' => $c['audience'],
            'iat' => $now,
            'exp' => $now + (int)$c['refresh_ttl'],
            'typ' => 'refresh',
            'sub' => $u->ccod_usuario,
        ], $c['secret']);
    }

    /**
     * Verifica un token. Lanza si esta expirado, mal firmado, alg invalido,
     * iss/aud no coinciden, etc. Retorna los claims como array asociativo.
     *
     * @return array<string,mixed>
     */
    public static function verify(string $token): array
    {
        $c = self::cfg();
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('JWT mal formado');
        }
        [$h64, $b64, $s64] = $parts;

        $headerJson = self::b64urlDecode($h64);
        $header = json_decode($headerJson, true);
        if (!is_array($header)) {
            throw new RuntimeException('JWT header invalido');
        }
        $alg = $header['alg'] ?? null;
        if (!is_string($alg) || $alg !== self::ALG) {
            // Bloquea explicitamente alg=none y otros algoritmos.
            throw new RuntimeException("JWT alg invalido: " . var_export($alg, true));
        }

        $expected = self::b64url(hash_hmac('sha256', "$h64.$b64", $c['secret'], true));
        if (!hash_equals($expected, $s64)) {
            throw new RuntimeException('JWT firma invalida');
        }

        $payload = json_decode(self::b64urlDecode($b64), true);
        if (!is_array($payload)) {
            throw new RuntimeException('JWT payload invalido');
        }

        $now    = time();
        $leeway = (int)$c['leeway'];

        if (isset($payload['nbf']) && $now + $leeway < (int)$payload['nbf']) {
            throw new RuntimeException('JWT no valido todavia (nbf)');
        }
        if (isset($payload['iat']) && $now + $leeway < (int)$payload['iat']) {
            throw new RuntimeException('JWT iat en el futuro');
        }
        if (!isset($payload['exp'])) {
            throw new RuntimeException('JWT sin exp');
        }
        if ($now - $leeway >= (int)$payload['exp']) {
            throw new RuntimeException('JWT expirado');
        }

        if (isset($payload['iss']) && $payload['iss'] !== $c['issuer']) {
            throw new RuntimeException('JWT iss invalido');
        }
        if (isset($payload['aud']) && $payload['aud'] !== $c['audience']) {
            throw new RuntimeException('JWT aud invalido');
        }

        return $payload;
    }

    /**
     * Encodea un payload como JWT firmado con HS256.
     *
     * @param array<string,mixed> $payload
     */
    private static function encode(array $payload, string $secret): string
    {
        $header  = self::b64url(json_encode(['typ' => 'JWT', 'alg' => self::ALG]));
        $body    = self::b64url(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $sig     = self::b64url(hash_hmac('sha256', "$header.$body", $secret, true));
        return "$header.$body.$sig";
    }

    private static function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $s): string
    {
        $rem = strlen($s) % 4;
        if ($rem) {
            $s .= str_repeat('=', 4 - $rem);
        }
        $decoded = base64_decode(strtr($s, '-_', '+/'), true);
        if ($decoded === false) {
            throw new RuntimeException('JWT base64url invalido');
        }
        return $decoded;
    }

    /**
     * setcookie con flags seguros. `Secure` solo se activa si la request
     * viene por HTTPS (sino el browser descartaria la cookie).
     */
    public static function setCookie(string $name, string $val, int $ttl): void
    {
        setcookie($name, $val, [
            'expires'  => $ttl > 0 ? time() + $ttl : 1,
            'path'     => '/',
            'secure'   => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    public static function clearCookie(string $name): void
    {
        self::setCookie($name, '', 0);
    }

    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
            return true;
        }
        return false;
    }

    public static function accessTtl(): int
    {
        return (int)self::cfg()['access_ttl'];
    }

    public static function refreshTtl(): int
    {
        return (int)self::cfg()['refresh_ttl'];
    }

    /** Para tests: limpia el cache de configuracion (re-lee `getenv`). */
    public static function resetCache(): void
    {
        self::$cfgCache = null;
    }
}
