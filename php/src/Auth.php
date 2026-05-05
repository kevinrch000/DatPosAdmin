<?php
/**
 * Helpers de autenticacion y sesion. Reemplaza ASP.NET Forms Authentication
 * + Session("objBEUser") del proyecto VB.NET.
 */

require_once __DIR__ . '/BE/BEUser.php';

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(BEUser $user): void
    {
        self::start();
        $_SESSION['objBEUser'] = $user;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
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
