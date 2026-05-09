<?php
/**
 * Consulta de empresas (read-only). Equivalente a ConsultaEmpresas.aspx
 * + ConsultaEmpresas.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/BL/BLConsultaEmpresas.php';
require_once __DIR__ . '/../../src/BL/BLConsultaUsuarios.php';

Auth::require_login();
$base = Auth::base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $body = Json::readBody();
    try {
        switch ($_GET['action']) {
            case 'ConsultasEmpresasPrincipal':
                $rows = (new BLConsultaEmpresas())->ConsultasEmpresasPrincipal(
                    (string)($body['ccod_empresa'] ?? ''),
                    (string)($body['ctarifas'] ?? ''),
                    (string)($body['cpais_origen'] ?? ''),
                    (string)($body['cstatus'] ?? '')
                );
                Json::respond(array_map(fn($r) => [
                    'ccod_empresa'     => (string)($r['ccod_empresa'] ?? ''),
                    'cdescripcion'     => (string)($r['cdsc_empresa'] ?? ''),
                    'cdoc'             => (string)($r['Documento'] ?? ''),
                    'cnombre_servidor' => (string)($r['cnombre_servidor'] ?? ''),
                    'cnombre_bd'       => (string)($r['cnombre_bd'] ?? ''),
                    'cpais_origen'     => (string)($r['Pais'] ?? ''),
                    'ctarifas'         => (string)($r['Tarifa'] ?? ''),
                    'cstatus' => ((string)($r['id_estado'] ?? '') === '1')
    ? 'Activo'
    : 'Inactivo',
                ], $rows));
            case 'ConsultaUsuariosPorEmpresa':
                $rows = (new BLConsultaUsuarios())->ConsultaUsuariosPorEmpresa((string)($body['empresa'] ?? ''));
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
site_layout_header('Consulta de Empresas');
include __DIR__ . '/ConsultaEmpresas.body.php';
site_layout_footer();
