<?php
/**
 * Wrapper sobre firebase/php-jwt: emite y verifica access/refresh tokens
 * y centraliza el manejo de cookies HttpOnly/SameSite.
 *
 * El secreto y los TTL vienen de:
 *   1) variables de entorno (`JWT_SECRET`, `JWT_ACCESS_TTL`, `JWT_REFRESH_TTL`)
 *   2) `php/config/config.php['jwt']` (override local).
 *
 * Esta clase **NO esta integrada todavia** con `Auth::login`. La integracion
 * va en un PR aparte (login emite cookie, require_login verifica, etc.).
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/BE/BEUser.php';

use Firebase\JWT\JWT as FbJWT;
use Firebase\JWT\Key;

class Jwt
{
    public const ACCESS_COOKIE  = 'dpa_jwt';
    public const REFRESH_COOKIE = 'dpa_jwt_r';

    private const ALG = 'HS256';

    /** @var array<string,mixed>|null */
    private static ?array $cfgCache = null;

    /**
     * @return array{secret:string,issuer:string,audience:string,access_ttl:int,refresh_ttl:int}
     */
    private static function cfg(): array
    {
        if (self::$cfgCache !== null) {
            return self::$cfgCache;
        }

        $cfg = [
            'secret'      => (string)(getenv('JWT_SECRET') ?: ''),
            'issuer'      => (string)(getenv('JWT_ISSUER')   ?: 'datpos-admin'),
            'audience'    => (string)(getenv('JWT_AUDIENCE') ?: 'datpos-admin-web'),
            'access_ttl'  => (int)(getenv('JWT_ACCESS_TTL')  ?: 3600),         // 1h
            'refresh_ttl' => (int)(getenv('JWT_REFRESH_TTL') ?: 60 * 60 * 24 * 7), // 7d
        ];

        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            $appCfg = require $configFile;
            if (is_array($appCfg) && isset($appCfg['jwt']) && is_array($appCfg['jwt'])) {
                $cfg = array_merge($cfg, $appCfg['jwt']);
            }
        }

        if ($cfg['secret'] === '') {
            throw new RuntimeException(
                "JWT_SECRET no configurado. Definir env var JWT_SECRET o " .
                "agregar 'jwt' => ['secret' => '<aleatorio>'] en php/config/config.php"
            );
        }

        return self::$cfgCache = $cfg;
    }

    /**
     * Genera un access token (TTL corto) con los datos basicos del usuario.
     */
    public static function issueAccess(BEUser $u): string
    {
        $c = self::cfg();
        $now = time();
        return FbJWT::encode([
            'iss' => $c['issuer'],
            'aud' => $c['audience'],
            'iat' => $now,
            'exp' => $now + (int)$c['access_ttl'],
            'typ' => 'access',
            'sub' => $u->ccod_usuario,
            'uid' => $u->id_usuario,
            'rol' => $u->id_rol,
            'emp' => $u->ccod_empresa,
        ], $c['secret'], self::ALG);
    }

    /**
     * Genera un refresh token (TTL largo) que solo lleva el sub.
     */
    public static function issueRefresh(BEUser $u): string
    {
        $c = self::cfg();
        $now = time();
        return FbJWT::encode([
            'iss' => $c['issuer'],
            'aud' => $c['audience'],
            'iat' => $now,
            'exp' => $now + (int)$c['refresh_ttl'],
            'typ' => 'refresh',
            'sub' => $u->ccod_usuario,
        ], $c['secret'], self::ALG);
    }

    /**
     * Verifica un token. Lanza si esta expirado, mal firmado, etc.
     *
     * @return array<string,mixed>
     */
    public static function verify(string $token): array
    {
        $c = self::cfg();
        $decoded = FbJWT::decode($token, new Key($c['secret'], self::ALG));
        return (array)$decoded;
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

    /**
     * Para tests: limpia el cache de configuracion (asi `getenv` se vuelve
     * a leer).
     */
    public static function resetCache(): void
    {
        self::$cfgCache = null;
    }
}
