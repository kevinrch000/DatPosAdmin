<?php
/**
 * Pagina de login. Equivalente a Account/Login.aspx + migadmin/LogOn.aspx
 * (replica el diseno de "Portal de Administracion" del proyecto original).
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/BL/BLUser.php';
require_once __DIR__ . '/../../src/BE/BEUser.php';

Auth::start();
$base  = Auth::base_path();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['UserName'] ?? '');
    $clave   = $_POST['Password'] ?? '';

    if ($usuario === '') {
        $error = 'Ingresar Usuario';
    } elseif ($clave === '') {
        $error = 'Ingresar Contraseña';
    } else {
        try {
            $rows = (new BLUser())->ValidarUsuario($usuario, $clave);
        } catch (Throwable $e) {
            $rows  = [];
            $error = 'Error al conectar con la base de datos: ' . $e->getMessage();
        }

        if ($rows && count($rows) > 0) {
            $row = $rows[0];
            $u = new BEUser();
            $u->id_usuario       = (int)($row['id_usuario'] ?? 0);
            $u->ccod_usuario     = (string)($row['ccod_usuario'] ?? $usuario);
            $u->cdsc_usuario     = (string)($row['cdsc_usuario'] ?? '');
            $u->id_rol           = (string)($row['id_rol'] ?? '');
            $u->ccod_empresa     = (string)($row['ccod_empresa'] ?? '');
            $u->cdsc_empresa     = (string)($row['cdsc_empresa'] ?? '');
            $u->cnombre_bd       = (string)($row['cnombre_bd'] ?? '');
            $u->cnombre_servidor = (string)($row['cnombre_servidor'] ?? '');
            Auth::login($u);
            header('Location: ' . $base . '/Interfaces/Home.php');
            exit;
        }
        if ($error === '') {
            // Diagnostico: mostrar contra que BD se valido (quitar en produccion)
            $dbInfo = '';
            try {
                $dbActual = Database::pdo()->query("SELECT DB_NAME()")->fetchColumn();
                $dbInfo = " [BD: $dbActual]";
            } catch (Throwable $ignore) {}
            $error = 'Usuario o Contraseña incorrecta' . $dbInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Portal de Administración - Datpos | Iniciar sesión</title>
    <link rel="shortcut icon" href="<?= $base ?>/assets/Styles/img/icon/icon_LogoCircle.png" />
    <link href="<?= $base ?>/assets/Styles/css/bootstrap.css"  rel="stylesheet" type="text/css" />
    <script src="<?= $base ?>/assets/Scripts/jquery-2.1.1.js"></script>
    <script src="<?= $base ?>/assets/Scripts/bootstrap.js"></script>
    <style>
        /* Override el `fieldset { margin: 1em 485px !important }` heredado de
           Site.css del proyecto original — aqui no se usa fieldset asi que
           solo cargamos bootstrap, no Site.css. */
        html, body { height: 100%; margin: 0; }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                linear-gradient(135deg, rgba(34,138,201,0.85), rgba(20, 90, 140, 0.85)),
                url('<?= $base ?>/assets/Styles/images/HOME2.jpg') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 16px;
        }
        .login-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            padding: 38px 36px 30px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 18px;
        }
        .login-brand img {
            width: 64px;
            height: 64px;
        }
        .login-card h2 {
            font-size: 22px;
            color: #228ac9;
            text-align: center;
            margin: 0 0 6px;
            font-weight: 600;
        }
        .login-card .subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .login-card .form-group {
            position: relative;
            margin-bottom: 16px;
        }
        .login-card label {
            display: block;
            font-size: 13px;
            color: #34495e;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .login-card .input-log {
            width: 100%;
            height: 42px;
            padding: 8px 12px 8px 40px;
            font-size: 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background: #fff;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            box-sizing: border-box;
        }
        .login-card .input-log:focus {
            outline: none;
            border-color: #228ac9;
            box-shadow: 0 0 0 3px rgba(34, 138, 201, 0.18);
        }
        .login-card .input-icon {
            position: absolute;
            left: 12px;
            top: 33px;
            color: #228ac9;
            font-size: 16px;
            pointer-events: none;
        }
        .login-card .btn-login {
            display: block;
            width: 100%;
            height: 44px;
            margin-top: 8px;
            background: #228ac9;
            color: #fff;
            border: 0;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease-in-out;
        }
        .login-card .btn-login:hover { background: #1c74ad; }
        .login-card .failure-msg {
            display: block;
            background: #fdecea;
            border: 1px solid #f5c2c0;
            color: #b03a2e;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 14px;
            text-align: center;
        }
        .login-footer {
            text-align: center;
            margin-top: 22px;
            color: rgba(255,255,255,0.85);
            font-size: 13px;
        }
        .login-footer a { color: #fff; text-decoration: underline; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <img src="<?= $base ?>/assets/Styles/img/icon/icon_LogoCircle.png" alt="Datpos" />
        </div>
        <h2>Portal de Administración</h2>
        <p class="subtitle">Especifique su nombre de usuario y contraseña</p>

        <?php if ($error !== ''): ?>
            <span class="failure-msg"><?= htmlspecialchars($error) ?></span>
        <?php endif; ?>

        <form method="post" action="<?= $base ?>/Account/Login.php" autocomplete="on">
            <div class="form-group">
                <label for="UserName">Usuario</label>
                <span class="glyphicon glyphicon-user input-icon"></span>
                <input class="input-log" id="UserName" name="UserName"
                       placeholder="Usuario" required autofocus
                       value="<?= htmlspecialchars($_POST['UserName'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label for="Password">Contraseña</label>
                <span class="glyphicon glyphicon-lock input-icon"></span>
                <input class="input-log" id="Password" name="Password"
                       placeholder="Contraseña" type="password" required maxlength="50" />
            </div>
            <button class="btn-login" type="submit">Ingresar</button>
        </form>
    </div>
    <div class="login-footer">
        © <?= date('Y') ?> DATPOS – Portal de Administración
    </div>
</div>
</body>
</html>
