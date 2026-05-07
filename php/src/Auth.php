<?php
/**
 * Helpers de autenticacion y sesion. Reemplaza ASP.NET Forms Authentication
 * + Session("objBEUser") del proyecto VB.NET.
 *
 * Integra el helper `Jwt` con la sesion PHP:
 *
 *  - `start()` arranca la sesion y, si esta vacia, intenta rehidratar
 *    `$_SESSION['objBEUser']` desde la cookie de access token.
 *  - `login($u)` guarda el user en sesion y emite las dos cookies JWT
 *    (access + refresh).
 *  - `logout()` borra sesion y cookies.
 *
 * Si `JWT_SECRET` no esta configurado, todo el path JWT degrada
 * silenciosamente y la app se comporta como antes (solo sesion PHP).
 */

require_once __DIR__ . '/BE/BEUser.php';
require_once __DIR__ . '/Jwt.php';

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['objBEUser'])) {
            self::tryLoadFromJwt();
        }
    }

    /**
     * Si la cookie de access token es valida, reconstruye el BEUser y lo
     * guarda en la sesion. Cualquier error (cookie ausente, token expirado,
     * firma invalida, falta de JWT_SECRET) se traga: la pagina protegida
     * va a redirigir al login normalmente.
     */
    private static function tryLoadFromJwt(): void
    {
        $token = $_COOKIE[Jwt::ACCESS_COOKIE] ?? '';
        if ($token === '') {
            return;
        }
        try {
            $claims = Jwt::verify($token);
            if (($claims['typ'] ?? '') !== 'access') {
                return;
            }
            $_SESSION['objBEUser'] = Jwt::userFromClaims($claims);
        } catch (Throwable $e) {
            // Token invalido/expirado/secret no configurado: no rehidratamos.
        }
    }

    public static function login(BEUser $user): void
    {
        self::start();
        $_SESSION['objBEUser'] = $user;
        // Emite las cookies JWT. Si el secret no esta configurado, degrada
        // a sesion PHP solamente (login sigue funcionando).
        try {
            Jwt::setCookie(Jwt::ACCESS_COOKIE,  Jwt::issueAccess($user),  Jwt::accessTtl());
            Jwt::setCookie(Jwt::REFRESH_COOKIE, Jwt::issueRefresh($user), Jwt::refreshTtl());
        } catch (Throwable $e) {
            // JWT no configurado: solo sesion.
        }
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        Jwt::clearCookie(Jwt::ACCESS_COOKIE);
        Jwt::clearCookie(Jwt::REFRESH_COOKIE);
    }

    public static function user(): ?BEUser
    {
        self::start();
        return $_SESSION['objBEUser'] ?? null;
    }

    /**
     * Si no hay sesion, redirige al login. Llamar al inicio de cada pagina
     * protegida (equivalente al chequeo Page_Load del Site.master.vb).
     */
    public static function require_login(): void
    {
        self::start();
        if (empty($_SESSION['objBEUser'])) {
            $base = self::base_path();
            header('Location: ' . $base . '/Account/Login.php');
            exit;
        }
    }

    public static function base_path(): string
    {
        $configFile = __DIR__ . '/../config/config.php';
        if (!file_exists($configFile)) {
            $configFile = __DIR__ . '/../config/config.example.php';
        }
        $cfg = require $configFile;
        return rtrim($cfg['base_path'] ?? '', '/');
    }
}
