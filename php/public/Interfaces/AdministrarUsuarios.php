<?php
/**
 * Administracion de usuarios (CRUD). Equivalente a AdministrarUsuarios.aspx
 * + AdministrarUsuarios.aspx.vb.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/BL/BLUsuario.php';
require_once __DIR__ . '/../../src/BL/BLEmpresa.php';
require_once __DIR__ . '/../../src/BE/BEUsuario.php';

Auth::require_login();
$base = Auth::base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $body = Json::readBody();
    try {
        $blUsuario = new BLUsuario();
        $blEmpresa = new BLEmpresa();
        switch ($_GET['action']) {
            case 'UsuariosAsociados':
                $rows = $blUsuario->UsuariosAsociados((string)($body['ccod_empresa'] ?? ''));
                Json::respond(array_map(fn($r) => [
                    'ccod_usuario' => (string)($r['ccod_usuario'] ?? ''),
                    'cdsc_usuario' => (string)($r['cdsc_usuario'] ?? ''),
                    'cdirec'       => (string)($r['cdirec'] ?? ''),
                    'cdsc_rol'     => (string)($r['cdsc_rol'] ?? ''),
                    'ccelular'     => (string)($r['ccelular'] ?? ''),
                    'cmail'        => (string)($r['cmail'] ?? ''),
                    'cstatus'      => (string)($r['id_estado'] ?? ''),
                ], $rows));
                break;
            case 'TablaEmpresas':
                $rows = $blEmpresa->CargarCompaniasConBDValida();
                Json::respond(array_map(fn($r) => [
                    'ccod_empresa'     => (string)$r['ccod_empresa'],
                    'cdescripcion'     => (string)$r['cdsc_empresa'],
                    'cnombre_servidor' => (string)$r['cnombre_servidor'],
                    'cnombre_bd'       => (string)$r['cnombre_bd'],
                ], $rows));
                break;
            case 'ConsultarUsuarios':
                $rows = $blUsuario->ConsultarUs();
                Json::respond(array_map(fn($r) => [
                    'id_usuario'   => (int)($r['id_usuario'] ?? 0),
                    'ccod_usuario' => (string)($r['ccod_usuario'] ?? ''),
                    'cdsc_usuario' => (string)($r['cdsc_usuario'] ?? ''),
                    'cpassw'       => (string)($r['cpassw'] ?? ''),
                    'cdirec'       => (string)($r['cdirec'] ?? ''),
                    'id_rol'       => (string)($r['id_rol'] ?? ''),
                    'ccod_empresa' => (string)($r['ccod_empresa'] ?? ''),
                    'cstatus'      => (string)($r['id_estado'] ?? ''),
                    'dfch_crea'    => (string)($r['dfch_crea'] ?? ''),
                ], $rows));
                break;
            case 'ConsultarUsuario':
                $rows = $blUsuario->CargarUsuario((string)($body['codigo'] ?? ''));
                $out = [];
                foreach ($rows as $r) {
                    $out[] = [
                        'id_usuario'   => (int)($r['id_usuario'] ?? 0),
                        'ccod_usuario' => (string)($r['ccod_usuario'] ?? ''),
                        'cdsc_usuario' => (string)($r['cdsc_usuario'] ?? ''),
                        'cpassw'       => (string)($r['cpassw'] ?? ''),
                        'cdirec'       => (string)($r['cdirec'] ?? ''),
                        'id_rol'       => (string)($r['id_rol'] ?? ''),
                        'ccod_empresa' => (string)($r['ccod_empresa'] ?? ''),
                        'cstatus'      => (string)($r['id_estado'] ?? ''),
                        'dfch_crea'    => (string)($r['dfch_crea'] ?? ''),
                        'cmail'        => (string)($r['cmail'] ?? ''),
                        'ctelf'        => (string)($r['ctelf'] ?? ''),
                        'ccelular'     => (string)($r['ccelular'] ?? ''),
                        'empresa'      => (string)($r['cdsc_empresa'] ?? ''),
                    ];
                }
                Json::respond($out);
                break;
            case 'GrabarUsuario':
                $usuarios  = $body['usuario'] ?? [];
                $operacion = (string)($body['operacion'] ?? '');
                if (!is_array($usuarios) || !isset($usuarios[0])) {
                    Json::respond([false, 'ERROR', 'Payload invalido']);
                }
                $obj = BEUsuario::fromArray($usuarios[0]);
                [$validOk, $validMsg] = $blUsuario->ValidarBDEmpresa($obj->ccod_empresa);
                if (!$validOk) {
                    Json::respond([false, 'ERROR', $validMsg]);
                }
                $resp = null;
                if ($operacion === 'nuevo') {
                    try {
                        $adminOk = $blUsuario->InsertarUsuarioAdmin($obj);
                    } catch (Throwable $ex) {
                        // Capturar violación de UNIQUE constraint (usuario duplicado)
                        $msg = $ex->getMessage();
                        if (stripos($msg, 'UNIQUE') !== false || stripos($msg, 'duplicate') !== false || stripos($msg, 'Violation') !== false) {
                            Json::respond([false, 'ERROR', 'El código de usuario "' . $obj->ccod_usuario . '" ya existe.']);
                        }
                        Json::respond([false, 'ERROR', 'Error al insertar usuario: ' . $msg]);
                    }
                    if ($adminOk) {
                        // Intentar insertar en BD hija (tenant) - si falla, es solo un warning
                        $tenantResp = $blUsuario->InsertarUsuario($obj, Auth::user());
                        if ($tenantResp && $tenantResp[0] === false) {
                            error_log('Warning: usuario creado en Admin pero fallo en BD hija: ' . ($tenantResp[2] ?? ''));
                        }
                        $resp = [true, 'OK', ''];
                    } else {
                        $resp = [false, 'ERROR', 'No se pudo insertar el usuario en DatPosAdmin'];
                    }
                } elseif ($operacion === 'editar') {
                    try {
                        $adminOk = $blUsuario->EditarUsuarioAdmin($obj);
                    } catch (Throwable $ex) {
                        Json::respond([false, 'ERROR', 'Error al editar usuario: ' . $ex->getMessage()]);
                    }
                    if ($adminOk) {
                        // Intentar editar en BD hija (tenant) - si falla, es solo un warning
                        $tenantResp = $blUsuario->EditarUsuario($obj, Auth::user());
                        if ($tenantResp && $tenantResp[0] === false) {
                            error_log('Warning: usuario editado en Admin pero fallo en BD hija: ' . ($tenantResp[2] ?? ''));
                        }
                        $resp = [true, 'OK', ''];
                    } else {
                        $resp = [false, 'ERROR', 'No se pudo editar el usuario en DatPosAdmin'];
                    }
                } else {
                    $resp = [false, 'ERROR', 'Operacion invalida: ' . $operacion];
                }
                Json::respond($resp);
                break;
            case 'Eliminar':
                $usuario     = (string)($body['usuario'] ?? '');
                $ipServidor  = (string)($body['ipServidor'] ?? '');
                $nomServidor = (string)($body['nomServidor'] ?? '');
                $ok = false;
                if ($blUsuario->EliminarUsuarioAdmin($usuario, Auth::user())) {
                    $ok = $blUsuario->EliminarUsuario($usuario, $ipServidor, $nomServidor, Auth::user());
                }
                Json::respond($ok);
                break;
            default:
                Json::error('Accion desconocida: ' . $_GET['action'], 400);
        }
    } catch (Throwable $e) {
        Json::error($e->getMessage());
    }
}

require __DIR__ . '/../Site.layout.php';
site_layout_header('Administrar Usuarios');
include __DIR__ . '/AdministrarUsuarios.body.php';
site_layout_footer();
