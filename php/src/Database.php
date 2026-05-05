<?php
/**
 * DatPOS - Conexion a base de datos (Multi-Tenant)
 * ----------------------------------------------------------------
 * Adaptacion del `database.php` original (sqlsrv / SQL Server) al
 * stack actual del proyecto: PHP 8 + PDO + MySQL/MariaDB.
 *
 * Reemplaza:  DA/DAConexionSQL.vb (VB.NET) y la version sqlsrv legacy.
 *
 * Soporta dos modos:
 *   1. Conexion ADMIN  (DatPosAdmin)   -> selectStored / executeStored
 *   2. Conexion TENANT (BD por empresa)-> selectStoredTenant / executeStoredTenant
 *
 * Parametros: se aceptan como array asociativo NOMBRADO (igual que la
 * version SQL Server, p.ej. `array('@ccod_usuario' => 'ADMIN')`) o como
 * array POSICIONAL (p.ej. `array('ADMIN', '123')`). En MySQL los SPs son
 * posicionales por definicion: si se pasan nombrados, conservamos el orden
 * de iteracion del array (PHP preserva el orden de insercion).
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
    // Construccion de la query (positional placeholders)
    // ============================================================

    /**
     * Convierte un array de parametros (nombrados o posicionales) a
     * placeholders posicionales `?, ?, ?` y la lista de valores en orden.
     *
     * @param array<int|string,mixed> $params
     * @return array{0:string,1:array<int,mixed>}
     */
    private static function buildCall(string $spName, array $params): array
    {
        if (empty($params)) {
            return ["CALL `$spName`()", []];
        }
        $values = array_values($params);
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        return ["CALL `$spName`($placeholders)", $values];
    }

    // ============================================================
    // Pool de conexiones PDO (admin + tenants)
    // ============================================================

    private static function connect(string $host, int $port, string $dbname, string $user, string $pass, string $charset = 'utf8mb4'): PDO
    {
        $key = sprintf('%s|%d|%s|%s', $host, $port, $dbname, $user);
        if (isset(self::$pool[$key])) {
            return self::$pool[$key];
        }

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host, $port, $dbname, $charset);

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]);

        // El dominio de id_estado incluye 0 (Bloqueado) y 1 (Habilitado).
        $pdo->exec("SET sql_mode = CONCAT(@@sql_mode, ',NO_AUTO_VALUE_ON_ZERO')");

        self::$pool[$key] = $pdo;
        return $pdo;
    }

    /** Conexion al ADMIN (DatPosAdmin). */
    public static function getAdminConnection(): PDO
    {
        $cfg = self::config()['db'];
        return self::connect(
            $cfg['host'],
            (int)$cfg['port'],
            $cfg['dbname'],
            $cfg['user'],
            $cfg['pass'],
            $cfg['charset'] ?? 'utf8mb4'
        );
    }

    /**
     * Conexion al TENANT, dinamica.
     * El objeto `objUsuario` puede ser BEUser o un array. Lee
     * `cnombre_servidor` y `cnombre_bd` (o sus aliases `cnomser`/`cnombd`
     * para compatibilidad con la version sqlsrv).
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

        // Permitir 'host:port'
        $host = $server;
        $port = (int)(self::config()['tenant']['port'] ?? 3306);
        if (str_contains($server, ':')) {
            [$host, $portStr] = explode(':', $server, 2);
            $port = (int)$portStr;
        }

        $tenantCfg = self::config()['tenant'] ?? [];
        $user = (string)($tenantCfg['user'] ?? 'datpos_tenant');
        $pass = (string)($tenantCfg['pass'] ?? '');

        try {
            return self::connect($host, $port, $database, $user, $pass);
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
     * Equivalente a `DAConexionSQL.selectstored()`.
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
            while ($stmt->nextRowset()) {
                // ignorar resultsets adicionales
            }
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("Error selectStored [$spName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta SP en BD Admin (sin SELECT). Retorna true/false.
     * Equivalente a `DAConexionSQL.executestored()`.
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
            while ($stmt->nextRowset()) {
                // ignorar
            }
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
     * Ejecuta SP en BD del Tenant y retorna filas. Equivalente a
     * `DAConexionSQL.selectstored_OtraConexion()`.
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
                // ignorar
            }
            return $rows ?: [];
        } catch (PDOException $e) {
            error_log("Error selectStoredTenant [$spName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ejecuta SP en BD del Tenant. Retorna true/false. Equivalente a
     * `DAConexionSQL.executestored_OtraConexion()`.
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
            while ($stmt->nextRowset()) {
                // ignorar
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error executeStoredTenant [$spName]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta SP en BD del Tenant y retorna el primer scalar (id) del
     * primer resultset. Equivalente a `executestored_OtraConexion_Id`.
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
                // ignorar
            }
            return $col === false ? 0 : (int)$col;
        } catch (PDOException $e) {
            error_log("Error executeStoredTenantReturnId [$spName]: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Ejecuta SP del Tenant con parametros OUT (MySQL OUT/INOUT).
     *
     * Acepta el mismo formato que la version sqlsrv:
     *   $params = [
     *     '@p_in'  => ['value' => 'x'],
     *     '@p_out' => ['direction' => 'output', 'type' => 'INT'],
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

        foreach ($params as $name => $def) {
            $clean = ltrim((string)$name, '@');
            if (isset($def['direction']) && $def['direction'] === 'output') {
                $declarations[] = "SET @{$clean} = NULL;";
                $callArgs[]     = "@{$clean}";
                $outNames[]     = $clean;
            } else {
                $callArgs[] = '?';
                $values[]   = $def['value'] ?? null;
            }
        }

        $sql = implode(' ', $declarations) . " CALL `$spName`(" . implode(',', $callArgs) . ');';

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            while ($stmt->nextRowset()) {
                // ignorar
            }
            $result = ['success' => true];
            if ($outNames) {
                $select = 'SELECT ' . implode(',', array_map(fn($n) => "@$n AS `$n`", $outNames));
                $row    = $pdo->query($select)->fetch();
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
