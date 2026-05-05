<?php
/** Acceso a datos para consultas de empresas. Equivalente a DA/DAConsultaEmpresas.vb. */

require_once __DIR__ . '/../Db.php';

class DAConsultaEmpresas
{
    /** @return array<int,array<string,mixed>> */
    public function ConsultasEmpresasPrincipal(string $ccod_empresa, string $ctarifas, string $cpais_origen, string $cstatus): array
    {
        if ($ctarifas === '') {
            $ctarifas = 'T';
        }
        if ($cpais_origen === '') {
            $cpais_origen = 'T';
        }
        if ($cstatus === '') {
            $cstatus = 'T';
        }
        return Db::callSp('webDatpos_buscarTarifa', [$ccod_empresa, $ctarifas, $cpais_origen, $cstatus]);
    }
}
