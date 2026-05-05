<?php
/** Lógica de negocio para consultas de empresas. Equivalente a BL/BLConsultaEmpresas.vb. */

require_once __DIR__ . '/../DA/DAConsultaEmpresas.php';

class BLConsultaEmpresas
{
    private DAConsultaEmpresas $da;

    public function __construct()
    {
        $this->da = new DAConsultaEmpresas();
    }

    public function ConsultasEmpresasPrincipal(string $ccod_empresa, string $ctarifas, string $cpais_origen, string $cstatus): array
    {
        return $this->da->ConsultasEmpresasPrincipal($ccod_empresa, $ctarifas, $cpais_origen, $cstatus);
    }
}
