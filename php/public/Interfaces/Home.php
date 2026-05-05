<?php
/**
 * Dashboard. Equivalente a Home.aspx + Home.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/BL/BLHome.php';

Auth::require_login();
$base = Auth::base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    try {
        $bl = new BLHome();
        switch ($_GET['action']) {
            case 'CantidadEmpresas':
                Json::respond($bl->ConsultarUs());
                break;
            case 'CantidadUsuarios':
                Json::respond($bl->ConsultarUssuario());
                break;
            default:
                Json::error('Accion desconocida: ' . $_GET['action'], 400);
        }
    } catch (Throwable $e) {
        Json::error($e->getMessage());
    }
}

require __DIR__ . '/../Site.layout.php';
site_layout_header('Dashboard - DATPOS', '<link href="' . $base . '/assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />');
?>
<input id="operacion" type="hidden" />
<input id="hdd_ultimafila" type="hidden" />
<input id="hdd_fila" type="hidden" value="0" />
<input id="hdd_numeromenus" type="hidden" value="1" />
<input id="hdd_numerofilas" type="hidden" />

<div class="c-content-center">
    <div class="menu idxconsul" style="text-align:center;padding-bottom:20px;">
        <img src="<?= $base ?>/assets/Styles/images/icon/icon_LogoCircle.png" style="width:7%;" />
        <h2>Bienvenido al Portal de Administración</h2>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon"><i class="material-icons">business</i></div>
                    <p class="card-category">Cantidad de Empresas</p>
                    <h3 id="cantidadTienda" class="card-title">0</h3>
                </div>
                <div class="card-footer"><div class="stats"></div></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                    <div class="card-icon"><i class="material-icons">supervisor_account</i></div>
                    <p class="card-category">Cantidad de Usuarios</p>
                    <h3 id="cantidadUsuario" class="card-title">0</h3>
                </div>
                <div class="card-footer"><div class="stats"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#id_titulo').text('Dashboard - DATPOS');
    CargarUsuar();
    CargarTa();
});

function CargarTa() {
    var obj = llenarobjeto('<?= $base ?>/Interfaces/Home.php?action=CantidadEmpresas');
    if (obj && obj[0]) $('#cantidadTienda').text(obj[0].cantidaTienda);
}

function CargarUsuar() {
    var obj = llenarobjeto('<?= $base ?>/Interfaces/Home.php?action=CantidadUsuarios');
    if (obj && obj[0]) $('#cantidadUsuario').text(obj[0].cantidaUsuarios);
}
</script>
<?php site_layout_footer(); ?>
