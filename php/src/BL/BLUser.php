<?php
/** Lógica de negocio de login. Equivalente a BL/BLUser.vb. */

require_once __DIR__ . '/../DA/DAUser.php';

class BLUser
{
    private DAUser $da;

    public function __construct()
    {
        $this->da = new DAUser();
    }

    public function ValidarUsuario(string $usuario, string $clave): array
    {
        return $this->da->ValidarUsuario($usuario, $clave);
    }
}
