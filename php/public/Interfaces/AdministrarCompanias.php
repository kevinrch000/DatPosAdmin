<?php
/**
 * Administracion de empresas (CRUD). Equivalente a AdministrarCompanias.aspx
 * + AdministrarCompanias.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/BL/BLEmpresa.php';
require_once __DIR__ . '/../../src/BE/BEEmpresa.php';

Auth::require_login();
$base = Auth::base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $body = Json::readBody();
    try {
        $bl = new BLEmpresa();
        switch ($_GET['action']) {
            case 'CargarDepartamento':
                $rows = $bl->CargarDepartamento($body['ccod_cia'] ?? null);
                Json::respond(array_map(
                    fn($r) => ['id' => (string)$r['id_departamento'], 'name' => (string)$r['cdescripcion']],
                    $rows
                ));
                break;
            case 'CargarProvincia':
                $rows = $bl->CargarProvincia((string)($body['id_departamento'] ?? ''));
                Json::respond(array_map(
                    fn($r) => ['id' => (string)$r['id_provincia'], 'name' => (string)$r['cdescripcion']],
                    $rows
                ));
                break;
            case 'CargarDistrito':
                $rows = $bl->CargarDistrito((string)($body['id_provincia'] ?? ''));
                Json::respond(array_map(
                    fn($r) => ['id' => (string)$r['id_distrito'], 'name' => (string)$r['cdescripcion']],
                    $rows
                ));
                break;
            case 'ConsultarEmpresas':
                $rows = $bl->CargarCompanias();
                $out = [];
                foreach ($rows as $r) {
                    $out[] = [
                        'id_empresa'        => (int)($r['id_empresa'] ?? 0),
                        'ccod_empresa'      => (string)($r['ccod_empresa'] ?? ''),
                        'cdescripcion'      => (string)($r['cdsc_empresa'] ?? ''),
                        'cnum_tribu'        => (string)($r['cnum_tribu'] ?? ''),
                        'cnombre_servidor'  => (string)($r['cnombre_servidor'] ?? ''),
                        'cnombre_bd'        => (string)($r['cnombre_bd'] ?? ''),
                        'cnombre_moneda'    => (string)($r['cnombre_moneda'] ?? ''),
                        'ctarifas'          => (string)($r['ctarifas'] ?? ''),
                        'dfch_crea'         => (string)($r['dfch_crea'] ?? ''),
                    ];
                }
                Json::respond($out);
                break;
            case 'ConsultarEmpresa':
                $codigo = (string)($body['codigo'] ?? '');
                $rows = $bl->CargarCompania($codigo);
                $out = [];
                foreach ($rows as $r) {
                    $out[] = [
                        'id_empresa'        => (int)($r['id_empresa'] ?? 0),
                        'ccod_empresa'      => (string)($r['ccod_empresa'] ?? ''),
                        'cdescripcion'      => (string)($r['cdsc_empresa'] ?? ''),
                        'cnum_tribu'        => (string)($r['cnum_tribu'] ?? ''),
                        'cnombre_servidor'  => (string)($r['cnombre_servidor'] ?? ''),
                        'cnombre_bd'        => (string)($r['cnombre_bd'] ?? ''),
                        'csimbolo_moneda'   => (string)($r['csimbolo_moneda'] ?? ''),
                        'cnombre_moneda'    => (string)($r['cnombre_moneda'] ?? ''),
                        'ctarifas'          => (string)($r['ctarifas'] ?? ''),
                        'nusuario_extra'    => (int)($r['nusuario_extra'] ?? 0),
                        'ntienda_extra'     => (int)($r['ntienda_extra'] ?? 0),
                        'cdepartamento'     => (string)($r['cdepartamento'] ?? ''),
                        'cprovincia'        => (string)($r['cprovincia'] ?? ''),
                        'cdistrito'         => (string)($r['cdistrito'] ?? ''),
                        'curbanizacion'     => (string)($r['curbanizacion'] ?? ''),
                        'cdomicilio'        => (string)($r['cdomicilio'] ?? ''),
                        'cubigeo'           => (string)($r['cubigeo'] ?? ''),
                        'nenviosunat'       => (string)($r['nenviosunat'] ?? ''),
                        'dfch_sunat'        => (string)($r['dfch_sunat'] ?? ''),
                        'ccod_cliente_emis' => (string)($r['ccod_cliente_emis'] ?? ''),
                        'dfch_vencimiento'  => (string)($r['dfch_vencimiento'] ?? ''),
                        'ctoken'            => (string)($r['ctoken'] ?? ''),
                        'ctip_facturador'   => (string)($r['ctip_facturador'] ?? ''),
                        'dfch_crea'         => (string)($r['dfch_crea'] ?? ''),
                    ];
                }
                Json::respond($out);
                break;
            case 'GrabarEmpresa':
                $empresa   = $body['empresa'] ?? [];
                $operacion = (string)($body['operacion'] ?? '');
                if (!is_array($empresa) || !isset($empresa[0]) || !is_array($empresa[0])) {
                    Json::respond(false);
                }
                $obj = BEEmpresa::fromArray($empresa[0]);
                $ok  = false;
                if ($operacion === 'nuevo') {
                    try {
                        $ok = $bl->InsertarCompania($obj);
                    } catch (Throwable $ex) {
                        $msg = $ex->getMessage();
                        if (stripos($msg, 'UNIQUE') !== false || stripos($msg, 'duplicate') !== false || stripos($msg, 'PRIMARY') !== false || stripos($msg, 'Violation') !== false) {
                            Json::error('El código de empresa "' . $obj->ccod_empresa . '" ya existe.', 409);
                        }
                        Json::error('Error al insertar empresa: ' . $msg);
                    }
                } elseif ($operacion === 'editar') {
                    try {
                        $ok = $bl->EditarCompania($obj);
                    } catch (Throwable $ex) {
                        Json::error('Error al editar empresa: ' . $ex->getMessage());
                    }
                }
                Json::respond($ok);
                break;
            case 'EliminarE':
                $cod = (string)($body['elimrempresa'] ?? '');
                if ($cod === '') {
                    Json::respond(false);
                }
                // Soft delete: desactivar empresa en vez de borrar permanentemente
                try {
                    $pdo = Database::getAdminConnection();
                    $stmt = $pdo->prepare("UPDATE dbo.Empresas SET id_estado = 0 WHERE ccod_empresa = ?");
                    $stmt->execute([$cod]);
                    Json::respond(true);
                } catch (Throwable $ex) {
                    Json::error('Error al desactivar empresa: ' . $ex->getMessage());
                }
                break;
            default:
                Json::error('Accion desconocida: ' . $_GET['action'], 400);
        }
    } catch (Throwable $e) {
        Json::error($e->getMessage());
    }
}

require __DIR__ . '/../Site.layout.php';
site_layout_header('Administrar Empresas');
include __DIR__ . '/AdministrarCompanias.body.php';
site_layout_footer();
