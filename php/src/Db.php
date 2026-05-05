<?php
/**
 * Conexion PDO a MySQL/MariaDB y helper para llamar procedimientos almacenados.
 * Equivalente a DA/DAConexionSQL.vb del proyecto VB.NET original.
 */

class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $configFile = __DIR__ . '/../config/config.php';
        if (!file_exists($configFile)) {
            $configFile = __DIR__ . '/../config/config.example.php';
        }
        $cfg = require $configFile;
        $db  = $cfg['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['dbname'],
            $db['charset']
        );

        self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]);

        // El dominio de id_estado incluye 0 (Bloqueado) y 1 (Habilitado), por
        // lo que necesitamos preservar el literal 0 al actualizar/insertar.
        self::$pdo->exec("SET sql_mode = CONCAT(@@sql_mode, ',NO_AUTO_VALUE_ON_ZERO')");

        return self::$pdo;
    }

    /**
     * Llama a un procedimiento almacenado por nombre con una lista posicional
     * de parametros. Devuelve el resultset como array de filas asociativas.
     *
     * Equivalente a DAConexionSQL.selectstored(SqlCommand) -> DataTable.
     *
     * @param string $name      Nombre del SP.
     * @param array<int,mixed> $params Parametros posicionales.
     * @return array<int,array<string,mixed>>
     */
    public static function callSp(string $name, array $params = []): array
    {
        $pdo = self::pdo();
        $placeholders = $params === [] ? '' : implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL `$name`($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($params));
        $rows = $stmt->fetchAll();
        // Drain any extra resultsets (some SPs return more than one).
        while ($stmt->nextRowset()) {
            // ignore
        }
        return $rows ?: [];
    }

    /**
     * Variante para SPs sin SELECT (UPDATE/INSERT/DELETE).
     * Equivalente a DAConexionSQL.executestored.
     */
    public static function execSp(string $name, array $params = []): bool
    {
        $pdo = self::pdo();
        $placeholders = $params === [] ? '' : implode(',', array_fill(0, count($params), '?'));
        $sql = "CALL `$name`($placeholders)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute(array_values($params));
            while ($stmt->nextRowset()) {
                // ignore
            }
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'ERROR EN BD PRINCIPAL (DatPosAdmin): ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
