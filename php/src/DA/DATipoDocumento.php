<?php
/** Acceso a datos para Tipos de Documento. Equivalente a DA/DATipoDocumento.vb. */

require_once __DIR__ . '/../Db.php';

class DATipoDocumento
{
    /** @return array<int,array<string,mixed>> */
    public function CargarTipoDocumento(): array
    {
        return Db::callSp('sp_consultatipodocumento');
    }
}
