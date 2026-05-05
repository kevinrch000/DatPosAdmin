<?php
/**
 * Consulta de usuarios (read-only). Equivalente a ConsultaUsuarios.aspx
 * + ConsultaUsuarios.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/BL/BLConsultaUsuarios.php';

Auth::require_login();
$base = Auth::base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $body = Json::readBody();
    try {
        $bl = new BLConsultaUsuarios();
        switch ($_GET['action']) {
            case 'ConsultasUsuariosPrincipal':
                $rows = $bl->ConsultasUsuariosPrincipal(
                    (string)($body['codigo'] ?? ''),
                    (string)($body['estado'] ?? '')
                );
                Json::respond(array_map(fn($r) => [
                    'ccod_empresa' => (string)($r['ccod_empresa'] ?? ''),
                    'cdsc_empresa' => (string)($r['cdsc_empresa'] ?? ''),
                    'ccod_usuario' => (string)($r['ccod_usuario'] ?? ''),
                    'cdsc_usuario' => (string)($r['cdsc_usuario'] ?? ''),
                    'cdir_usuario' => (string)($r['cdirec'] ?? ''),
                    'cdsc_rol'     => (string)($r['cdsc_rol'] ?? ''),
                    'cpais_origen' => (string)($r['cdsc_departamento'] ?? ''),
                    'cstatus'      => (string)($r['id_estado'] ?? ''),
                    'ccelular'     => (string)($r['ccelular'] ?? ''),
                ], $rows));
            case 'ConsultaUsuariosPorEmpresa':
                $rows = $bl->ConsultaUsuariosPorEmpresa((string)($body['empresa'] ?? ''));
                Json::respond(array_map(fn($r) => [
                    'ccod_empresa'  => (string)($r['ccod_empresa'] ?? ''),
                    'cdescripcion'  => (string)($r['cdsc_empresa'] ?? ''),
                    'countUsuarios' => (string)($r['TotalUsuarios'] ?? ''),
                ], $rows));
            default:
                Json::error('Accion desconocida: ' . $_GET['action'], 400);
        }
    } catch (Throwable $e) {
        Json::error($e->getMessage());
    }
}

require __DIR__ . '/../Site.layout.php';
site_layout_header('Consulta de Usuarios');
include __DIR__ . '/ConsultaUsuarios.body.php';
site_layout_footer();
