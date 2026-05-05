<?php
/** Lógica de negocio de Estados. Equivalente a BL/BLEstado.vb. */

require_once __DIR__ . '/../DA/DAEstado.php';

class BLEstado
{
    private DAEstado $da;

    public function __construct()
    {
        $this->da = new DAEstado();
    }

    public function CargarEstados(): array
    {
        return $this->da->CargarEstados();
    }
}
