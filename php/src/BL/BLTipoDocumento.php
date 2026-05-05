<?php
/** Lógica de negocio de Tipos de Documento. Equivalente a BL/BLTipoDocumento.vb. */

require_once __DIR__ . '/../DA/DATipoDocumento.php';

class BLTipoDocumento
{
    private DATipoDocumento $da;

    public function __construct()
    {
        $this->da = new DATipoDocumento();
    }

    public function CargarTipoDocumento(): array
    {
        return $this->da->CargarTipoDocumento();
    }
}
