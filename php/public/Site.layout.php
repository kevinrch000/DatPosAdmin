<?php
/**
 * Layout principal con menu vertical y header. Equivalente a Site.master /
 * Site.master.vb del proyecto VB.NET.
 *
 * Uso: una pagina protegida llama a `site_layout_header($titulo)` al inicio
 * y `site_layout_footer()` al final, dejando el contenido entre ambas.
 */

require_once __DIR__ . '/../src/Auth.php';

function site_layout_header(string $titulo = 'Administración', string $extraHead = ''): void
{
    Auth::require_login();
    $base = Auth::base_path();
    $user = Auth::user();
    $usuario = $user ? htmlspecialchars($user->cdsc_usuario) : '';
    $empresa = $user ? htmlspecialchars($user->cdsc_empresa) : '';
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Portal de Administración - Datpos | <?= htmlspecialchars($titulo) ?></title>
    <link rel="shortcut icon" href="https://www.datpos.com/wp-content/uploads/2020/10/favicon_Mesa-de-trabajo-1.png" />

    <link href="<?= $base ?>/assets/Styles/Site.css"          rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/MenuVer.css"       rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/css/jquery-confirm.css" rel="stylesheet" type="text/css" />

    <script src="<?= $base ?>/assets/Scripts/jquery-2.1.1.js"></script>
    <script src="<?= $base ?>/assets/Scripts/bootstrap.js"></script>
    <script src="<?= $base ?>/assets/Scripts/chart.js"></script>
    <script src="<?= $base ?>/assets/Scripts/highcharts.js"></script>
    <script src="<?= $base ?>/assets/Scripts/data.js"></script>
    <script src="<?= $base ?>/assets/Scripts/jquery-confirm.js"></script>

    <link href="<?= $base ?>/assets/Styles/css/alertify.core.css"    rel="stylesheet" type="text/css" />
    <link href="<?= $base ?>/assets/Styles/css/alertify.default.css" rel="stylesheet" type="text/css" />
    <script src="<?= $base ?>/assets/Scripts/alertify.js"></script>

    <script src="<?= $base ?>/assets/Javascript/Comun.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <link href="<?= $base ?>/assets/Styles/ddl_autocomplete.css" rel="stylesheet" type="text/css" />
    <script src="<?= $base ?>/assets/Javascript/ddl_autocomplete.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link href="<?= $base ?>/assets/Styles/Moderno.css" rel="stylesheet" type="text/css" />

    <script src="<?= $base ?>/assets/Javascript/FileSaver.js"></script>
    <?= $extraHead ?>
    <style>
        h1 { position: relative; float: left; background: rgb(238,244,247); color: Black; font-size: 2.5em; }
        .navbar-default { background-color: #f8f8f800; border-color: #f8f8f800; }
        .navbar { min-height: 0px; }
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #f5f5f5; opacity: 1;
        }
    </style>
</head>
<body>
<div id="menuver" class="nav-side-menu" onmouseover="MostrarMenu();">
    <div class="brand c-logo-menu">
        <a href="<?= $base ?>/Interfaces/Home.php">
            <img src="<?= $base ?>/assets/Styles/img/icon/icon_LogoCircle.png" style="width:33px;" /></a>
    </div>
    <span class="glyphicon glyphicon-align-justify toggle-btn" style="font-size:18px; margin-top:5px;"
          data-toggle="collapse" data-target="#menu-content"></span>
    <div class="menu-list">
        <ul id="menu-content" class="menu-content collapse out">
            <li id="1_li_Administracion" data-toggle="collapse" data-target="#1_ul_Administracion" class="collapsed">
                <a href="#"><img src="<?= $base ?>/assets/Styles/images/icono_adm.png" style="width:25px;">Administración</a>
            </li>
            <ul class="sub-menu collapse" id="1_ul_Administracion">
                <li id="2_li_Empresas">
                    <a href="<?= $base ?>/Interfaces/AdministrarCompanias.php" style="left:32px;">
                        <img src="<?= $base ?>/assets/Styles/images/empresa.png" style="width:17px; top:12px; left:33px;">Empresas</a>
                </li>
                <li id="2_li_Usuarios">
                    <a href="<?= $base ?>/Interfaces/AdministrarUsuarios.php" style="left:32px;">
                        <img src="<?= $base ?>/assets/Styles/images/usuarios.png" style="width:17px; top:12px; left:33px;">Usuarios</a>
                </li>
            </ul>
            <li id="1_li_Consultas" data-toggle="collapse" data-target="#1_ul_Consultas" class="collapsed">
                <a href="#"><img src="<?= $base ?>/assets/Styles/images/icon/icon_saldo.png" style="width:25px;">Consulta</a>
            </li>
            <ul id="1_ul_Consultas" class="sub-menu collapse">
                <li id="3_li_Usuario">
                    <a href="<?= $base ?>/Interfaces/ConsultaUsuarios.php" style="left:32px;">
                        <img src="<?= $base ?>/assets/Styles/images/buscarUsuario.png" style="width:17px; top:12px; left:33px;">Usuarios</a>
                </li>
                <li id="3_li_Empresa">
                    <a href="<?= $base ?>/Interfaces/ConsultaEmpresas.php" style="left:32px;">
                        <img src="<?= $base ?>/assets/Styles/images/buscarEmpresa.png" style="width:17px; top:12px; left:33px;">Empresas</a>
                </li>
            </ul>
            <li><a id="btnCerrarSesion" href="<?= $base ?>/Account/Logout.php">
                <img src="<?= $base ?>/assets/Styles/images/cerrarsesion.png" style="width:21px;" />Cerrar Sesion</a></li>
        </ul>
    </div>
</div>

<div id="content" class="c-content" style="background-color:White">
    <header>
        <span class="c-menu-toggle" id="btnMenu" onclick="mostrar();"></span>
        <span class="header-title" id="id_titulo"><?= htmlspecialchars($titulo) ?></span>
        <div class="c-menu-user">
            <img src="<?= $base ?>/assets/Styles/img/avatar.png" alt="Avatar" />
            <div class="info-name-type">
                <span id="id_empresa"><?= $empresa ?></span>
                <span id="id_usuario"><?= $usuario ?></span>
            </div>
            <li class="dropdown" style="list-style:none; top:-4px;">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="material-icons">settings</i></a>
                <ul class="dropdown-menu" style="left:-161px;">
                    <li><a href="#">Mi perfil</a></li>
                    <li><a href="#" data-toggle="modal" data-target="#MoCambioPass" onclick="LimpiarPopUp();">Cambiar Contraseña</a></li>
                    <li><a href="#">Configuracion</a></li>
                </ul>
            </li>
        </div>
    </header>

    <div id="c-loader"><img src="<?= $base ?>/assets/Styles/img/loader.gif" /></div>
    <script type="text/javascript">$('#c-loader').show();</script>
<?php
}

function site_layout_footer(): void
{
    ?>
</div><!-- /#content -->

<ul id="contextMenu" class="dropdown-menu" role="menu" style="display:none">
    <div class="input-group">
        <a><img src="<?= Auth::base_path() ?>/assets/Styles/images/icon/icon_exel_c.png"
                style="width:14px;margin-right:8px;margin-left:5px"></a>
        <label style="color:#333;">Exportar a Excel</label>
    </div>
</ul>

<script type="text/javascript">
    function MostrarMenu() {
        $("#menuver").removeClass("hiddenmenuvertical-menu");
        $("#content").removeClass("hiddenmenuvertical-header");
    }
    function OcultarMenu() {
        $("#menuver").addClass("hiddenmenuvertical-menu");
        $("#content").addClass("hiddenmenuvertical-header");
    }
    $('#c-loader').show();
    setTimeout(function () { $('#c-loader').hide(); }, 1000);
    function mostrar() {
        if ($("#menuver").hasClass("hiddenmenuvertical-menu") == true) {
            $("#menuver").removeClass("hiddenmenuvertical-menu");
            $("#content").removeClass("hiddenmenuvertical-header");
        } else {
            $("#menuver").addClass("hiddenmenuvertical-menu");
            $("#content").addClass("hiddenmenuvertical-header");
        }
    }
</script>
</body>
</html>
<?php
}
