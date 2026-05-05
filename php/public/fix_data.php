<?php
/**
 * Script para corregir datos en DatPosAdmin.
 * - Actualiza cnombre_servidor de Empresas a localhost\SQLEXPRESS
 * - Actualiza la contraseña del usuario admin a "admin"
 *
 * Acceder via navegador: http://localhost:PUERTO/fix_data.php
 * ELIMINAR despues de ejecutar.
 */

require_once __DIR__ . '/../src/Database.php';

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><title>Fix Data - DatPosAdmin</title>';
echo '<style>body{font-family:Consolas,monospace;padding:20px;background:#1e1e1e;color:#d4d4d4}';
echo '.ok{color:#4ec9b0}.err{color:#f44747}.warn{color:#dcdcaa}h2{color:#569cd6}';
echo 'pre{background:#2d2d2d;padding:12px;border-radius:6px}</style></head><body>';
echo '<h1>🔧 Correccion de Datos - DatPosAdmin</h1>';

try {
    $pdo = Database::getAdminConnection();
    $dbName = $pdo->query("SELECT DB_NAME()")->fetchColumn();
    echo "<p class=\"ok\">✔ Conectado a: <b>$dbName</b></p>";
} catch (Throwable $e) {
    echo '<p class="err">✖ Error de conexion: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</body></html>';
    exit;
}

// ============================================================
// 1. Corregir cnombre_servidor en Empresas
// ============================================================
echo '<h2>1. Corregir cnombre_servidor en Empresas</h2>';

try {
    // Mostrar valor actual
    $antes = $pdo->query("SELECT ccod_empresa, cnombre_servidor, cnombre_bd FROM Empresas")->fetchAll();
    echo '<p class="warn">Antes:</p><pre>';
    foreach ($antes as $row) {
        echo $row['ccod_empresa'] . ' → servidor: ' . $row['cnombre_servidor'] . ' | bd: ' . $row['cnombre_bd'] . "\n";
    }
    echo '</pre>';

    // Actualizar DESKTOP-E3VFI77 → localhost\SQLEXPRESS
    $stmt = $pdo->prepare("UPDATE Empresas SET cnombre_servidor = ? WHERE cnombre_servidor = ?");
    $stmt->execute(['localhost\\SQLEXPRESS', 'DESKTOP-E3VFI77']);
    $affected = $stmt->rowCount();

    if ($affected > 0) {
        echo "<p class=\"ok\">✔ Actualizado $affected registro(s): DESKTOP-E3VFI77 → localhost\\SQLEXPRESS</p>";
    } else {
        echo '<p class="warn">⚠ No se encontraron registros con DESKTOP-E3VFI77 (ya estaba corregido?)</p>';
    }

    // Mostrar valor despues
    $despues = $pdo->query("SELECT ccod_empresa, cnombre_servidor, cnombre_bd FROM Empresas")->fetchAll();
    echo '<p class="ok">Despues:</p><pre>';
    foreach ($despues as $row) {
        echo $row['ccod_empresa'] . ' → servidor: ' . $row['cnombre_servidor'] . ' | bd: ' . $row['cnombre_bd'] . "\n";
    }
    echo '</pre>';
} catch (Throwable $e) {
    echo '<p class="err">✖ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// ============================================================
// 2. Corregir contraseña del usuario admin
// ============================================================
echo '<h2>2. Corregir contraseña del usuario "admin"</h2>';

try {
    // Mostrar valor actual
    $user = $pdo->query("SELECT ccod_usuario, cpassw FROM Usuarios WHERE ccod_usuario = 'admin'")->fetch();
    if ($user) {
        echo '<p class="warn">Contraseña actual: "' . htmlspecialchars($user['cpassw']) . '"</p>';

        // Actualizar contraseña a "admin"
        $stmt = $pdo->prepare("UPDATE Usuarios SET cpassw = ? WHERE ccod_usuario = ?");
        $stmt->execute(['admin', 'admin']);
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            echo '<p class="ok">✔ Contraseña actualizada a "admin"</p>';
        } else {
            echo '<p class="warn">⚠ No se actualizo (ya tenia esa contraseña?)</p>';
        }
    } else {
        echo '<p class="err">✖ Usuario "admin" no encontrado</p>';
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// ============================================================
// 3. Verificacion final: test de login
// ============================================================
echo '<h2>3. Verificacion: test login admin/admin</h2>';

try {
    $testRows = Database::selectStored('webDatpos_validarUsuario', ['admin', 'admin']);
    if ($testRows && count($testRows) > 0) {
        echo '<p class="ok">✔ Login "admin"/"admin" EXITOSO (' . count($testRows) . ' fila)</p>';
        echo '<pre>' . htmlspecialchars(print_r($testRows[0], true)) . '</pre>';
    } else {
        echo '<p class="err">✖ Login "admin"/"admin" sigue fallando (0 filas)</p>';
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '<hr><p style="color:#666">Eliminar este archivo despues de ejecutar.</p>';
echo '</body></html>';
