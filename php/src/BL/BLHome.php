<?php
/** Lógica de negocio para Home. Equivalente a BL/BLHome.vb. */

require_once __DIR__ . '/../DA/DAHome.php';

class BLHome
{
    private DAHome $da;

    public function __construct()
    {
        $this->da = new DAHome();
    }

    public function ConsultarUs(): array
    {
        return $this->da->ConsultarUsuarios();
    }

    public function ConsultarUssuario(): array
    {
        return $this->da->ConsultarUssuario();
    }
}
