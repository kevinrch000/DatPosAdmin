<?php
/** Lógica de negocio de Roles. Equivalente a BL/BLRol.vb. */

require_once __DIR__ . '/../DA/DARol.php';

class BLRol
{
    private DARol $da;

    public function __construct()
    {
        $this->da = new DARol();
    }

    public function CargarRoles(): array
    {
        return $this->da->CargarRoles();
    }
}
