<?php
/** Lógica de negocio para consultas de usuarios. Equivalente a BL/BLConsultaUsuarios.vb. */

require_once __DIR__ . '/../DA/DAConsultaUsuarios.php';

class BLConsultaUsuarios
{
    private DAConsultaUsuarios $da;

    public function __construct()
    {
        $this->da = new DAConsultaUsuarios();
    }

    public function ConsultasUsuariosPrincipal(string $codigo, string $estado): array
    {
        return $this->da->ConsultasUsuariosPrincipal($codigo, $estado);
    }

    public function ConsultaUsuariosPorEmpresa(string $empresa): array
    {
        return $this->da->ConsultaUsuariosPorEmpresa($empresa);
    }
}
