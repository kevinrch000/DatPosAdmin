<?php
/**
 * Thin alias de la clase principal `Database` (multi-tenant, PDO/MySQL).
 *
 * Db::pdo() / Db::callSp() / Db::execSp() existen para compatibilidad con
 * los DA escritos al inicio de la migracion. Codigo nuevo deberia usar
 * `Database::selectStored / executeStored / selectStoredTenant /
 *  executeStoredTenant` directamente.
 */

require_once __DIR__ . '/Database.php';

class Db
{
    /** Conexion PDO admin (DatPosAdmin). */
    public static function pdo(): PDO
    {
        return Database::getAdminConnection();
    }

    /**
     * Llama un SP en la BD admin y devuelve filas.
     *
     * @param array<int|string,mixed> $params
     * @return array<int,array<string,mixed>>
     */
    public static function callSp(string $name, array $params = []): array
    {
        return Database::selectStored($name, $params);
    }

    /**
     * Llama un SP en la BD admin sin SELECT.
     *
     * @param array<int|string,mixed> $params
     */
    public static function execSp(string $name, array $params = []): bool
    {
        return Database::executeStored($name, $params);
    }
}
