<?php
/** Acceso a datos para Roles. Equivalente a DA/DARol.vb. */

require_once __DIR__ . '/../Db.php';

class DARol
{
    /** @return array<int,array<string,mixed>> */
    public function CargarRoles(): array
    {
        return Db::callSp('sp_consultarroles');
    }
}
