<?php
/** Acceso a datos para Home. Equivalente a DA/DAHome.vb. */

require_once __DIR__ . '/../Db.php';

class DAHome
{
    /** @return array<int,array<string,mixed>> */
    public function ConsultarUsuarios(): array
    {
        return Db::callSp('webDatpos_contadorEmpresa');
    }

    /** @return array<int,array<string,mixed>> */
    public function ConsultarUssuario(): array
    {
        return Db::callSp('webDatpos_contadorUsuario');
    }
}
