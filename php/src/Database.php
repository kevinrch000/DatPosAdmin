<?php
/**
 * DatPOS - Conexion a base de datos (Microsoft SQL Server, multi-tenant)
 * ----------------------------------------------------------------
 * Reemplaza:  DA/DAConexionSQL.vb (VB.NET) y la version MySQL/PDO previa.
 *
 * Soporta dos modos:
 *   1. Conexion ADMIN  (DatPosAdmin)   -> selectStored / executeStored
 *   2. Conexion TENANT (BD por empresa)-> selectStoredTenant / executeStoredTenant
 *
 * Implementacion:
 *   - Driver: PDO + ODBC Driver 18 for SQL Server (`pdo_sqlsrv`).
 *   - Llamadas: `EXEC <sp> ?, ?, ...` con parametros posicionales (compatible
 *     con T-SQL). Se aceptan parametros nombrados (p.ej. `'@p' => 'x'`) o
 *     posicionales (p.ej. `['x', 'y']`). El orden de iteracion del array
 *     se preserva (PHP mantiene orden de insercion).
 *
 * Configuracion:
 *   - Conexion ADMIN  : se lee de  config/config.php  ('db' admin).
 *   - Conexion TENANT : se lee de  config/config.php  ('tenant' user/pass)
 *                       y se combina con el server/bd que viene en el
 *                       objeto `BEUser` autenticado.
 */

class Database
{
    /** @var array<string,PDO> Cache de conexiones por DSN+usuario. */
    private static array $pool = [];

    /** Cache de la configuracion completa. */
    private static ?array $cfg = null;

    // ============================================================
    // Configuracion
    // ============================================================

    private static function config(): array
    {
        if (self::$cfg !== null) {
            return self::$cfg;
        }
        $configFile = __DIR__ . '/../config/config.php';
        if (!file_exists($configFile)) {
            $configFile = __DIR__ . '/../config/config.example.php';
        }
        self::$cfg = require $configFile;
        return self::$cfg;
    }

    // ============================================================
    // Construccion de la query (placeholders posicionales SQL Server)
    // ============================================================

    /**
     * Convierte un array de parametros (nombrados o posicionales) en una
     * sentencia `EXEC <sp> ?, ?, ?` con la lista de valores en orden.
     *
     * @param array<int|string,mixed> $params
     * @return array{0:string,1:array<int,mixed>}
     */
    private static function buildCall(string $spName, array $params): array
    {
        $sp = '[dbo].[' . str_replace([']', '['], '', $spName) . ']';
        if (empty($params)) {
            return ["EXEC $sp", []];
        }
        $values = array_values($params);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        return ["EXEC $sp $placeholders", $values];
    }

    // ============================================================
    // Pool de conexiones PDO/sqlsrv
    // ============================================================

    /**
     * @param array<string,mixed> $extra opciones DSN extra (ej.
     *        TrustServerCertificate, ConnectionPooling).
     */
    private static function connect(string $server, string $database, string $user, string $pass, array $extra = []): PDO
    {
        $key = sprintf('%s|%s|%s', $server, $database, $user);
        if (isset(self::$pool[$key])) {
            return self::$pool[$key];
        }

        // Parametros DSN para pdo_sqlsrv. Server admite "host", "host,port",
        // "tcp:host", o "host\\INSTANCE".
        $dsnParts = [
            "Server=$server",
            "Database=$database",
        ];
        $defaults = [
            'TrustServerCertificate' => '1',
            'Encrypt'                => '0',
            'APP'                    => 'DatPosAdmin',
        ];
        foreach ($defaults + $extra as $k => $v) {
            $dsnParts[] = "$k=$v";
        }
        $dsn = 'sqlsrv:' . implode(';', $dsnParts);

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
            PDO::SQLSRV_ATTR_DIRECT_QUERY => true,
            PDO::SQLSRV_ATTR_ENCODING     => PDO::SQLSRV_ENCODING_UTF8,
        ]);

        self::$pool[$key] = $pdo;
        return $pdo;
    }

    /** Conexion al ADMIN (DatPosAdmin). */
    public static function getAdminConnection(): PDO
    {
        $cfg = self::config()['db'];
        return self::connect(
            (string)$cfg['server'],
            (string)$cfg['dbname'],
            (string)$cfg['user'],
            (string)$cfg['pass'],
            (array)($cfg['extra'] ?? [])
        );
    }

    /**
     * Conexion al TENANT, dinamica.
     * El objeto `objUsuario` puede ser BEUser o un array. Lee
     * `cnombre_servidor` y `cnombre_bd`.
     */
    public static function getTenantConnection(object|array|null $objUsuario): ?PDO
    {
        if (!$objUsuario) {
            error_log('Database::getTenantConnection: objUsuario es NULL');
            return null;
        }
        $get = function ($k) use ($objUsuario) {
            if (is_array($objUsuario)) return $objUsuario[$k] ?? null;
            return $objUsuario->{$k} ?? null;
        };

        $server   = (string)($get('cnombre_servidor') ?? $get('cnomser') ?? '');
        $database = (string)($get('cnombre_bd')       ?? $get('cnombd')  ?? '');

        if ($server === '' || $database === '') {
            error_log("Database::getTenantConnection: server o database vacios. server='$server', dbname='$database'");
            return null;
        }

        $tenantCfg = self::config()['tenant'] ?? [];
        $user = (string)($tenantCfg['user'] ?? '');
        $pass = (string)($tenantCfg['pass'] ?? '');

        try {
            return self::connect($server, $database, $user, $pass, (array)($tenantCfg['extra'] ?? []));
        } catch (PDOException $e) {
            error_log("Database::getTenantConnection [$server/$database]: " . $e->getMessage());
            return null;
        }
    }

    // ============================================================
    // ADMIN
    // ============================================================

    /**
     * Ejecuta SP en BD Admin y retorna las filas como array asociativo.
     *
     * @param array<int|string,mixed> $params
     * @return array<int,array<string,mixed>>
     */
    public static function selectStored(string $spName, array $params = []): array
    {
        try {
            $pdo = self::getAdminConnection();
        } catch (PDOException $e) {
            error_log("Error conexion Admin: " . $e->getMessage());
            return [];
        }
        [$sql, $values] = self::buildCall($spName, $params);
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $rows = $stmt->fetchAll();
            // SQL Server puede devolver resultsets vacios primero (rowcount).
            while ($stmt->nextRowset()) {
                if (empty($rows)) {
                    $extra = $stmt->fetchAll();
                    if (!empty($extra)) {
                        $rows = $extra;
                    }
                }
            }
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("Error selectStored [$spName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta SP en BD Admin (sin SELECT). Retorna true/false.
     *
     * @param array<int|string,mixed> $params
     */
    public static function executeStored(string $spName, array $params = []): bool
    {
        try {
            $pdo = self::getAdminConnection();
        } catch (PDOException $e) {
            throw new RuntimeException(
                'ERROR EN BD PRINCIPAL (DatPosAdmin): ' . $e->getMessage(),
                0, $e
            );
        }
        [$sql, $values] = self::buildCall($spName, $params);
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            do {
                // drenar todos los resultsets
            } while ($stmt->nextRowset());
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'ERROR EN BD PRINCIPAL (DatPosAdmin): ' . $e->getMessage(),
                0, $e
            );
        }
    }

    // ============================================================
    // TENANT
    // ============================================================

    /**
     * Ejecuta SP en BD del Tenant y retorna filas.
     *
     * @param array<int|string,mixed> $params
     * @return array<int,array<string,mixed>>
     */
    public static function selectStoredTenant(string $spName, array $params = [], object|array|null $objUsuario = null): array
    {
        $pdo = self::getTenantConnection($objUsuario);
        if ($pdo === null) {
            return [];
        }
        [$sql, $values] = self::buildCall($spName, $params);
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $rows = $stmt->fetchAll();
            while ($stmt->nextRowset()) {
                if (empty($rows)) {
                    $extra = $stmt->fetchAll();
                    if (!empty($extra)) {
                        $rows = $extra;
                    }
                }
            }
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("Error selectStoredTenant [$spName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta SP en BD del Tenant. Retorna true/false.
     *
     * @param array<int|string,mixed> $params
     */
    public static function executeStoredTenant(string $spName, array $params = [], object|array|null $objUsuario = null): bool
    {
        $pdo = self::getTenantConnection($objUsuario);
        if ($pdo === null) {
            return false;
        }
        [$sql, $values] = self::buildCall($spName, $params);
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            do {
                // drenar
            } while ($stmt->nextRowset());
            return true;
        } catch (PDOException $e) {
            error_log("Error executeStoredTenant [$spName]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta SP en BD del Tenant y retorna el primer scalar (id).
     *
     * @param array<int|string,mixed> $params
     */
    public static function executeStoredTenantReturnId(string $spName, array $params = [], object|array|null $objUsuario = null): int
    {
        $pdo = self::getTenantConnection($objUsuario);
        if ($pdo === null) {
            return 0;
        }
        [$sql, $values] = self::buildCall($spName, $params);
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $col = $stmt->fetchColumn();
            while ($stmt->nextRowset()) {
                if ($col === false) {
                    $col = $stmt->fetchColumn();
                }
            }
            return $col === false ? 0 : (int)$col;
        } catch (PDOException $e) {
            error_log("Error executeStoredTenantReturnId [$spName]: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Ejecuta SP del Tenant con parametros OUT (T-SQL OUTPUT params).
     *
     * Acepta el mismo formato que la version sqlsrv legacy:
     *   $params = [
     *     '@p_in'  => ['value' => 'x'],
     *     '@p_out' => ['direction' => 'output', 'type' => 'NVARCHAR(200)'],
     *   ];
     *
     * Devuelve un array con `success` + valores OUT por nombre.
     *
     * @param array<string,array<string,mixed>> $params
     * @return array<string,mixed>
     */
    public static function executeStoredTenantWithOutput(string $spName, array &$params, object|array|null $objUsuario = null): array
    {
        $pdo = self::getTenantConnection($objUsuario);
        if ($pdo === null) {
            return ['success' => false];
        }

        $declarations = [];
        $callArgs     = [];
        $values       = [];
        $outNames     = [];
        $outTypes     = [];

        foreach ($params as $name => $def) {
            $clean   = ltrim((string)$name, '@');
            $atName  = '@' . $clean;
            if (isset($def['direction']) && $def['direction'] === 'output') {
                $sqlType        = $def['type'] ?? 'NVARCHAR(MAX)';
                $declarations[] = "DECLARE $atName $sqlType;";
                $callArgs[]     = "$atName OUTPUT";
                $outNames[]     = $clean;
                $outTypes[]     = $sqlType;
            } else {
                $callArgs[] = '?';
                $values[]   = $def['value'] ?? null;
            }
        }

        $sp  = '[dbo].[' . str_replace([']', '['], '', $spName) . ']';
        $sql = implode(' ', $declarations) . " EXEC $sp " . implode(', ', $callArgs) . ';';
        if ($outNames) {
            $sql .= ' SELECT ' . implode(', ',
                array_map(fn($n) => "@$n AS [$n]", $outNames)) . ';';
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            $result = ['success' => true];
            if ($outNames) {
                $row = false;
                do {
                    $candidate = $stmt->fetch();
                    if ($candidate !== false) {
                        $row = $candidate;
                        break;
                    }
                } while ($stmt->nextRowset());

                if ($row) {
                    foreach ($row as $k => $v) {
                        if (isset($params['@' . $k])) {
                            $params['@' . $k]['value'] = $v;
                        } elseif (isset($params[$k])) {
                            $params[$k]['value'] = $v;
                        }
                        $result[$k] = $v;
                    }
                }
            } else {
                while ($stmt->nextRowset()) {
                    // drenar
                }
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error executeStoredTenantWithOutput [$spName]: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ============================================================
    // Helpers de bajo nivel reutilizados por los DA existentes
    // ============================================================

    /** Conexion PDO admin (compat con codigo legacy que usa Db::pdo()). */
    public static function pdo(): PDO
    {
        return self::getAdminConnection();
    }
}
