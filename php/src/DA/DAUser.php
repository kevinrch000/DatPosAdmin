<?php
/** Acceso a datos para login de usuarios. Equivalente a DA/DAUser.vb. */

require_once __DIR__ . '/../Db.php';

class DAUser
{
    /** @return array<int,array<string,mixed>> */
    public function ValidarUsuario(string $usuario, string $clave): array
    {
        // Log de diagnostico: verificar que la conexion apunta a DatPosAdmin
        try {
            $dbActual = Db::pdo()->query("SELECT DB_NAME()")->fetchColumn();
            error_log("[DAUser::ValidarUsuario] BD conectada: '$dbActual' | usuario: '$usuario'");
        } catch (Throwable $e) {
            error_log("[DAUser::ValidarUsuario] No se pudo determinar BD: " . $e->getMessage());
        }

        return Db::callSp('webDatpos_validarUsuario', [$usuario, $clave]);
    }
}
