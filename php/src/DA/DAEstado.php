<?php
/** Acceso a datos para Estados. Equivalente a DA/DAEstado.vb. */

require_once __DIR__ . '/../Db.php';

class DAEstado
{
    /** @return array<int,array<string,mixed>> */
    public function CargarEstados(): array
    {
        return Db::callSp('sp_consultaestados');
    }
}
