<?php
/** Acceso a datos para login de usuarios. Equivalente a DA/DAUser.vb. */

require_once __DIR__ . '/../Db.php';

class DAUser
{
    /**
     * Busca un usuario activo por su codigo. Devuelve la fila completa
     * (incluyendo `cpassw` y `cpassw_bcrypt`) para que la verificacion del
     * password se haga en PHP via `password_verify`.
     *
     * @return array<int,array<string,mixed>>
     */
    public function BuscarPorCodigo(string $usuario): array
    {
        try {
            $dbActual = Db::pdo()->query("SELECT DB_NAME()")->fetchColumn();
            error_log("[DAUser::BuscarPorCodigo] BD conectada: '$dbActual' | usuario: '$usuario'");
        } catch (Throwable $e) {
            error_log("[DAUser::BuscarPorCodigo] No se pudo determinar BD: " . $e->getMessage());
        }

        return Db::callSp('webDatpos_validarUsuario', [$usuario]);
    }

    /**
     * Persiste un nuevo hash bcrypt para un usuario (rehash perezoso post-login
     * y migracion desde plaintext).
     */
    public function ActualizarPasswordHash(string $usuario, string $hash): void
    {
        Db::execSp('webDatpos_actualizarPasswordHash', [$usuario, $hash]);
    }

    /**
     * @deprecated Usar `BuscarPorCodigo` + verificacion en PHP. Se mantiene
     * por compatibilidad con llamadas existentes que esperan la firma vieja.
     *
     * @return array<int,array<string,mixed>>
     */
    public function ValidarUsuario(string $usuario, string $clave): array
    {
        unset($clave);
        return $this->BuscarPorCodigo($usuario);
    }
}
