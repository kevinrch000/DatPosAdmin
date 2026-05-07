<?php
/**
 * Script de diagnostico para verificar la conexion multi-tenant.
 * Acceder via navegador: http://localhost:PUERTO/check_db.php
 * ELIMINAR en produccion.
 */

require_once __DIR__ . '/../src/Database.php';

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><title>Diagnostico DB - DatPosAdmin</title>';
echo '<style>body{font-family:Consolas,monospace;padding:20px;background:#1e1e1e;color:#d4d4d4}';
echo '.ok{color:#4ec9b0}.err{color:#f44747}.warn{color:#dcdcaa}h2{color:#569cd6}';
echo 'pre{background:#2d2d2d;padding:12px;border-radius:6px;overflow-x:auto}</style></head><body>';
echo '<h1>🔍 Diagnostico de Conexion - DatPosAdmin</h1>';

// ============================================================
// 1. Verificar config
// ============================================================
echo '<h2>1. Configuracion</h2>';

$configFile = __DIR__ . '/../config/config.php';
$usingExample = false;
if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/../config/config.example.php';
    $usingExample = true;
}

if ($usingExample) {
    echo '<p class="warn">⚠ Usando config.example.php (no existe config.php)</p>';
} else {
    echo '<p class="ok">✔ Usando config.php</p>';
}

$cfg = require $configFile;
echo '<pre>';
echo "Server : " . ($cfg['db']['server'] ?? '(no definido)') . "\n";
echo "DBName : " . ($cfg['db']['dbname'] ?? '(no definido)') . "\n";
echo "User   : " . ($cfg['db']['user'] ?: '(vacio → Windows Auth)') . "\n";
echo "Pass   : " . ($cfg['db']['pass'] ? '****' : '(vacio)') . "\n";
echo '</pre>';

// ============================================================
// 2. Conexion Admin
// ============================================================
echo '<h2>2. Conexion Admin (DatPosAdmin)</h2>';

try {
    $pdo = Database::getAdminConnection();
    $dbName = $pdo->query("SELECT DB_NAME()")->fetchColumn();
    $serverName = $pdo->query("SELECT @@SERVERNAME")->fetchColumn();

    if ($dbName === ($cfg['db']['dbname'] ?? 'DatPosAdmin')) {
        echo "<p class=\"ok\">✔ Conectado correctamente a: <b>$dbName</b> en $serverName</p>";
    } else {
        echo "<p class=\"err\">✖ BD INCORRECTA: conectado a <b>$dbName</b> en lugar de <b>{$cfg['db']['dbname']}</b></p>";
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error de conexion: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</body></html>';
    exit;
}

// ============================================================
// 3. Verificar SP webDatpos_validarUsuario
// ============================================================
echo '<h2>3. Stored Procedure: webDatpos_validarUsuario</h2>';

try {
    $sp = $pdo->query("
        SELECT name, type_desc, create_date, modify_date
        FROM sys.procedures
        WHERE name = 'webDatpos_validarUsuario'
    ")->fetch();

    if ($sp) {
        echo '<p class="ok">✔ SP encontrado en ' . $dbName . '</p>';
        echo '<pre>';
        echo "Nombre  : " . $sp['name'] . "\n";
        echo "Tipo    : " . $sp['type_desc'] . "\n";
        echo "Creado  : " . $sp['create_date'] . "\n";
        echo "Modific.: " . $sp['modify_date'] . "\n";
        echo '</pre>';

        // Mostrar definicion del SP
        $def = $pdo->query("
            SELECT definition
            FROM sys.sql_modules
            WHERE object_id = OBJECT_ID('webDatpos_validarUsuario')
        ")->fetchColumn();

        if ($def) {
            echo '<h3>Definicion del SP:</h3>';
            echo '<pre>' . htmlspecialchars($def) . '</pre>';
        }
    } else {
        echo '<p class="err">✖ SP "webDatpos_validarUsuario" NO EXISTE en ' . $dbName . '</p>';
        echo '<p class="warn">→ Esto explica por que el login falla. El SP debe crearse en DatPosAdmin.</p>';

        // Buscar si existe en otras BDs
        try {
            $otherDbs = $pdo->query("
                SELECT name FROM sys.databases
                WHERE name NOT IN ('master','tempdb','model','msdb')
                ORDER BY name
            ")->fetchAll(PDO::FETCH_COLUMN);

            echo '<h3>Buscando SP en otras bases de datos:</h3>';
            foreach ($otherDbs as $otherDb) {
                try {
                    $safeDb = str_replace(["'", "]", "["], '', $otherDb);
                    $exists = $pdo->query("
                        SELECT COUNT(*) FROM [$safeDb].sys.procedures
                        WHERE name = 'webDatpos_validarUsuario'
                    ")->fetchColumn();
                    if ($exists > 0) {
                        echo "<p class=\"warn\">  ⚠ Encontrado en: <b>$otherDb</b></p>";
                    }
                } catch (Throwable $ignore) {}
            }
        } catch (Throwable $ignore) {}
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error consultando SP: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// ============================================================
// 4. Test de login
// ============================================================
echo '<h2>4. Test de login (admin / admin)</h2>';

try {
    require_once __DIR__ . '/../src/BL/BLUser.php';
    $bl = new BLUser();
    $testRows = $bl->ValidarUsuario('admin', 'admin');
    if ($testRows && count($testRows) > 0) {
        echo '<p class="ok">✔ Login "admin"/"admin" retorno ' . count($testRows) . ' fila(s)</p>';
        echo '<pre>' . htmlspecialchars(print_r($testRows[0], true)) . '</pre>';
    } else {
        echo '<p class="err">✖ Login "admin"/"admin" retorno 0 filas (credenciales no encontradas en ' . $dbName . ')</p>';
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error ejecutando SP: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// ============================================================
// 5. Inspeccionar tabla Usuarios (registro "admin")
// ============================================================
echo '<h2>5. Inspeccion de tabla Usuarios (usuario "admin")</h2>';

try {
    // 5a. Buscar el usuario sin filtrar contraseña ni estado
    $user = $pdo->query("
        SELECT id_usuario, ccod_usuario, cpassw,
               ISNULL(cpassw_bcrypt, '') AS cpassw_bcrypt,
               id_estado, id_rol, ccod_empresa
        FROM Usuarios
        WHERE ccod_usuario = 'admin'
    ")->fetch();

    if ($user) {
        echo '<p class="ok">✔ Usuario "admin" encontrado en tabla Usuarios</p>';
        echo '<pre>';
        echo "id_usuario    : " . $user['id_usuario'] . "\n";
        echo "ccod_usuario  : " . $user['ccod_usuario'] . "\n";
        echo "cpassw        : " . ($user['cpassw'] ?: '(vacio)') . "\n";
        echo "cpassw_bcrypt : " . ($user['cpassw_bcrypt'] !== '' ? '(hash bcrypt presente)' : '(vacio)') . "\n";
        echo "id_estado     : " . $user['id_estado'] . ($user['id_estado'] == 1 ? ' (activo)' : ' ⚠ INACTIVO') . "\n";
        echo "id_rol        : " . $user['id_rol'] . "\n";
        echo "ccod_empresa  : " . $user['ccod_empresa'] . "\n";
        echo '</pre>';

        // 5b. Verificar contraseña: bcrypt primero, fallback a plaintext legacy
        $bcrypt = (string)$user['cpassw_bcrypt'];
        $legacy = (string)$user['cpassw'];
        if ($bcrypt !== '' && password_verify('admin', $bcrypt)) {
            echo '<p class="ok">✔ Contraseña "admin" valida via bcrypt</p>';
        } elseif ($legacy !== '' && hash_equals($legacy, 'admin')) {
            echo '<p class="warn">⚠ Contraseña "admin" valida via plaintext legacy (se hasheara al primer login)</p>';
        } else {
            echo '<p class="err">✖ Contraseña NO coincide con "admin" (ni bcrypt ni legacy)</p>';
        }

        // 5c. Verificar estado
        if ((int)$user['id_estado'] !== 1) {
            echo '<p class="err">✖ Usuario INACTIVO (id_estado = ' . $user['id_estado'] . '). El SP requiere id_estado = 1</p>';
        }

        // 5d. Verificar que ccod_empresa existe en Empresas
        $empresa = $pdo->query("
            SELECT ccod_empresa, cdsc_empresa, cnombre_bd, cnombre_servidor
            FROM Empresas
            WHERE ccod_empresa = '" . str_replace("'", "''", $user['ccod_empresa']) . "'
        ")->fetch();

        if ($empresa) {
            echo '<p class="ok">✔ Empresa "' . htmlspecialchars($user['ccod_empresa']) . '" encontrada en tabla Empresas</p>';
            echo '<pre>';
            echo "ccod_empresa      : " . $empresa['ccod_empresa'] . "\n";
            echo "cdsc_empresa      : " . $empresa['cdsc_empresa'] . "\n";
            echo "cnombre_bd        : " . $empresa['cnombre_bd'] . "\n";
            echo "cnombre_servidor  : " . $empresa['cnombre_servidor'] . "\n";
            echo '</pre>';
        } else {
            echo '<p class="err">✖ Empresa "' . htmlspecialchars($user['ccod_empresa']) . '" NO EXISTE en tabla Empresas</p>';
            echo '<p class="warn">→ El INNER JOIN del SP falla por esto. El login nunca devolvera filas.</p>';

            // Mostrar empresas disponibles
            $empresas = $pdo->query("SELECT ccod_empresa, cdsc_empresa FROM Empresas ORDER BY ccod_empresa")->fetchAll();
            if ($empresas) {
                echo '<h3>Empresas disponibles:</h3><pre>';
                foreach ($empresas as $emp) {
                    echo $emp['ccod_empresa'] . ' → ' . $emp['cdsc_empresa'] . "\n";
                }
                echo '</pre>';
            } else {
                echo '<p class="err">✖ Tabla Empresas esta VACIA</p>';
            }
        }
    } else {
        echo '<p class="err">✖ Usuario "admin" NO EXISTE en tabla Usuarios de DatPosAdmin</p>';

        // Mostrar usuarios existentes
        $usuarios = $pdo->query("SELECT ccod_usuario, cdsc_usuario, id_estado FROM Usuarios ORDER BY ccod_usuario")->fetchAll();
        if ($usuarios) {
            echo '<h3>Usuarios existentes en DatPosAdmin:</h3><pre>';
            foreach ($usuarios as $usr) {
                $estado = (int)$usr['id_estado'] === 1 ? 'activo' : 'inactivo';
                echo $usr['ccod_usuario'] . ' → ' . $usr['cdsc_usuario'] . " ($estado)\n";
            }
            echo '</pre>';
        } else {
            echo '<p class="err">✖ Tabla Usuarios esta VACIA</p>';
        }
    }
} catch (Throwable $e) {
    echo '<p class="err">✖ Error inspeccionando Usuarios: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '<hr><p style="color:#666">Eliminar este archivo en produccion.</p>';
echo '</body></html>';

