<?php
/**
 * Pagina de login. Equivalente a Account/Login.aspx + Login.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/BL/BLUser.php';
require_once __DIR__ . '/../../src/BE/BEUser.php';

Auth::start();
$base  = Auth::base_path();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['UserName'] ?? '';
    $clave   = $_POST['Password'] ?? '';

    if ($usuario === '' || $clave === '') {
        $error = 'Por favor ingrese usuario y contraseña.';
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
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Portal de Administración - Datpos | Iniciar sesión</title>
    <link rel="shortcut icon" href="https://www.datpos.com/wp-content/uploads/2020/10/favicon_Mesa-de-trabajo-1.png" />
    <link href="<?= $base ?>/assets/Styles/Site.css"           rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/css/bootstrap.css"  rel="stylesheet" type="text/css" />
    <script src="<?= $base ?>/assets/Scripts/jquery-2.1.1.js"></script>
    <script src="<?= $base ?>/assets/Scripts/bootstrap.js"></script>
    <style>
        body { background: #228ac9; }
        .login-card {
            background: #fff; max-width: 360px; margin: 80px auto; padding: 30px 28px;
            border-radius: 8px; box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }
        .login-card h2 { font-size: 1.6em; margin-top:0; color: #228ac9; }
        .login-card .form-control { margin-bottom: 12px; }
        .login-card .btn-primary { width: 100%; background:#228ac9; border:0; }
        .failureNotification { color:#c0392b; display:block; margin:8px 0; }
    </style>
</head>
<body>
<div class="login-card">
    <h2>Iniciar sesión</h2>
    <p>Especifique su nombre de usuario y contraseña.</p>
    <?php if ($error !== ''): ?>
        <span class="failureNotification"><?= htmlspecialchars($error) ?></span>
    <?php endif; ?>
    <form method="post" action="<?= $base ?>/Account/Login.php">
        <fieldset>
            <legend class="sr-only">Información de cuenta</legend>
            <label for="UserName">Nombre de usuario:</label>
            <input class="form-control" id="UserName" name="UserName" required autofocus />
            <label for="Password">Contraseña:</label>
            <input class="form-control" id="Password" name="Password" type="password" required />
            <label><input type="checkbox" name="RememberMe" /> Mantenerme conectado</label>
        </fieldset>
        <p class="submitButton" style="margin-top:14px;">
            <button class="btn btn-primary" type="submit">Iniciar sesión</button>
        </p>
    </form>
</div>
</body>
</html>
