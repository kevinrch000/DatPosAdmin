<?php
/** Acceso a datos para login de usuarios. Equivalente a DA/DAUser.vb. */

require_once __DIR__ . '/../Db.php';

class DAUser
{
    /** @return array<int,array<string,mixed>> */
    public function ValidarUsuario(string $usuario, string $clave): array
    {
        return Db::callSp('webDatpos_validarUsuario', [$usuario, $clave]);
    }
}
