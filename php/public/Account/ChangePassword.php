<?php
/**
 * Cambio de contrasena. POST con accion `cambiar` ejecuta el SP
 * `webDatpos_cambiarContrasena`. Equivalente a ChangePassword.aspx.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/Db.php';
require_once __DIR__ . '/../../src/DA/DAUser.php';

Auth::require_login();
$base = Auth::base_path();
$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = $_POST;
    if (!$body) {
        $body = Json::readBody();
    }
    $action = $_GET['action'] ?? ($body['action'] ?? 'cambiar');
    if ($action === 'cambiar') {
        try {
            $cpassw   = (string)($body['cpassw']   ?? '');
            $newpassw = (string)($body['newpassw'] ?? '');
            if ($cpassw === '' || $newpassw === '') {
                Json::respond(0);
            }

            $da   = new DAUser();
            $rows = $da->BuscarPorCodigo($user->ccod_usuario);
            if (!$rows) {
                Json::respond(0);
            }
            $bcrypt = (string)($rows[0]['cpassw_bcrypt'] ?? '');
            $legacy = (string)($rows[0]['cpassw']        ?? '');

            // Si ya hay hash bcrypt, SOLO bcrypt autentica. Si esta vacio,
            // se acepta el legacy (usuario que nunca migro).
            if ($bcrypt !== '') {
                $ok = password_verify($cpassw, $bcrypt);
            } else {
                $ok = $legacy !== '' && hash_equals($legacy, $cpassw);
            }
            if (!$ok) {
                Json::respond(0);
            }

            $rows = Db::callSp('webDatpos_cambiarContrasena', [
                $user->ccod_usuario,
                password_hash($newpassw, PASSWORD_DEFAULT),
            ]);
            $resultado = $rows[0]['resultado'] ?? 0;
            Json::respond((int)$resultado);
        } catch (Throwable $e) {
            Json::error($e->getMessage());
        }
    }
}

require __DIR__ . '/../Site.layout.php';
site_layout_header('Cambiar contraseña');
?>
<div class="content" style="padding:30px;">
    <h2>Cambiar contraseña</h2>
    <form id="frmCambiarPass" onsubmit="return cambiar(event);" style="max-width:380px;">
        <div class="form-group">
            <label>Contraseña actual</label>
            <input class="form-control" type="password" id="cpassw" required />
        </div>
        <div class="form-group">
            <label>Contraseña nueva</label>
            <input class="form-control" type="password" id="newpassw" required />
        </div>
        <button class="btn btn-primary" type="submit">Guardar</button>
    </form>
</div>
<script>
function cambiar(ev) {
    ev.preventDefault();
    $.ajax({
        type: 'POST',
        url: '<?= $base ?>/Account/ChangePassword.php?action=cambiar',
        data: JSON.stringify({
            cpassw: $('#cpassw').val(),
            newpassw: $('#newpassw').val()
        }),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        success: function (response) {
            if (response.d == 1) {
                alertify.success('Contraseña actualizada correctamente.');
            } else {
                alertify.error('La contraseña actual no es correcta.');
            }
        },
        error: function (xhr) {
            alertify.error('Error: ' + xhr.responseText);
        }
    });
    return false;
}
</script>
<?php site_layout_footer(); ?>
