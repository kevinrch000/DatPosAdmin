<?php
/**
 * Endpoint de refresh: si la cookie de refresh es valida, busca el usuario
 * en la BD por `ccod_usuario` (sub claim) y emite un nuevo access token.
 *
 * No se modifica la sesion PHP; Auth::start() la rehidrata en el siguiente
 * request a partir del access cookie nuevo.
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Jwt.php';
require_once __DIR__ . '/../../src/Json.php';
require_once __DIR__ . '/../../src/DA/DAUser.php';
require_once __DIR__ . '/../../src/BE/BEUser.php';

header('Content-Type: application/json; charset=utf-8');

$token = $_COOKIE[Jwt::REFRESH_COOKIE] ?? '';
if ($token === '') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'no_refresh_cookie']);
    exit;
}

try {
    $claims = Jwt::verify($token);
} catch (Throwable $e) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'invalid_refresh', 'msg' => $e->getMessage()]);
    exit;
}

if (($claims['typ'] ?? '') !== 'refresh') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'wrong_typ']);
    exit;
}

$ccod = (string)($claims['sub'] ?? '');
if ($ccod === '') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'missing_sub']);
    exit;
}

try {
    $rows = (new DAUser())->BuscarPorCodigo($ccod);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'db_error', 'msg' => $e->getMessage()]);
    exit;
}

if (!$rows || count($rows) === 0) {
    // Usuario borrado o desactivado entre login y refresh: invalidar cookies.
    Jwt::clearCookie(Jwt::ACCESS_COOKIE);
    Jwt::clearCookie(Jwt::REFRESH_COOKIE);
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'user_not_found']);
    exit;
}
$row = $rows[0];

$u = new BEUser();
$u->id_usuario       = (int)($row['id_usuario'] ?? 0);
$u->ccod_usuario     = (string)($row['ccod_usuario'] ?? $ccod);
$u->cdsc_usuario     = (string)($row['cdsc_usuario'] ?? '');
$u->id_rol           = (string)($row['id_rol'] ?? '');
$u->ccod_empresa     = (string)($row['ccod_empresa'] ?? '');
$u->cdsc_empresa     = (string)($row['cdsc_empresa'] ?? '');
$u->cnombre_bd       = (string)($row['cnombre_bd'] ?? '');
$u->cnombre_servidor = (string)($row['cnombre_servidor'] ?? '');

try {
    Jwt::setCookie(Jwt::ACCESS_COOKIE, Jwt::issueAccess($u), Jwt::accessTtl());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'jwt_config', 'msg' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'ok'         => true,
    'access_ttl' => Jwt::accessTtl(),
    'sub'        => $u->ccod_usuario,
]);
