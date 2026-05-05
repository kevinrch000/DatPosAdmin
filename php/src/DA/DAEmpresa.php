<?php
/**
 * Acceso a datos para Empresas. Equivalente a DA/DAEmpresa.vb.
 */

require_once __DIR__ . '/../Db.php';
require_once __DIR__ . '/../BE/BEEmpresa.php';

class DAEmpresa
{
    /** @return array<int,array<string,mixed>> */
    public function CargarDepartamento(?string $ccod_cia = null): array
    {
        return Db::callSp('webDatpos_cargarDepartamentos');
    }

    /** @return array<int,array<string,mixed>> */
    public function CargarProvincia(string $id_departamento): array
    {
        return Db::callSp('webDatpos_cargarProvincias', [$id_departamento]);
    }

    /** @return array<int,array<string,mixed>> */
    public function CargarDistrito(string $id_provincia): array
    {
        return Db::callSp('webDatpos_cargarDistritos', [$id_provincia]);
    }

    /** @return array<int,array<string,mixed>> */
    public function CargarCompanias(): array
    {
        return Db::callSp('webDatpos_consultarEmpresas');
    }

    /** @return array<int,array<string,mixed>> */
    public function CargarCompania(string $cod): array
    {
        return Db::callSp('webDatpos_consultarEmpresa', [$cod]);
    }

    public function InsertarCompania(BEEmpresa $obj): bool
    {
        return Db::execSp('webDatpos_insertarEmpresas', [
            $obj->ccod_empresa,
            $obj->cdescripcion,
            $obj->cnombre_bd,
            $obj->cnombre_servidor,
            $obj->cnum_tribu,
            $obj->csimbolo_moneda,
            $obj->cnombre_moneda,
            $obj->ctarifas,
            $obj->nusuario_extra,
            $obj->ntienda_extra,
            $obj->cdepartamento,
            $obj->cprovincia,
            $obj->cdistrito,
            $obj->curbanizacion,
            $obj->cdomicilio,
            $obj->cubigeo,
            $obj->nenviosunat,
            self::parseDmYDate($obj->dfch_sunat),
            $obj->ccod_cliente_emis,
            self::parseDmYDate($obj->dfch_vencimiento),
            $obj->ctoken,
            $obj->ctip_facturador,
            1,
            $obj->cpais_origen,
        ]);
    }

    public function EditarCompania(BEEmpresa $obj): bool
    {
        return Db::execSp('webDatpos_editarEmpresa', [
            $obj->ccod_empresa,
            $obj->cdescripcion,
            $obj->cnum_tribu,
            $obj->cnombre_bd,
            $obj->cnombre_servidor,
            $obj->csimbolo_moneda,
            $obj->cnombre_moneda,
            $obj->ctarifas,
            $obj->nusuario_extra,
            $obj->ntienda_extra,
            $obj->cdepartamento,
            $obj->cdistrito,
            $obj->cprovincia,
            $obj->curbanizacion,
            $obj->cdomicilio,
            $obj->cubigeo,
            $obj->nenviosunat,
            self::parseDmYDate($obj->dfch_sunat),
            self::parseDmYDate($obj->dfch_vencimiento),
            $obj->ctoken,
            $obj->ctip_facturador,
        ]);
    }

    public function EliminarEmpresa(string $cod): bool
    {
        return Db::execSp('sp_eliminarempresa', [$cod]);
    }

    /**
     * Convierte una fecha 'dd/MM/yyyy' del frontend a 'YYYY-MM-DD' para MySQL,
     * o NULL si esta vacia.
     */
    private static function parseDmYDate(?string $value): ?string
    {
        if ($value === null || trim((string)$value) === '') {
            return null;
        }
        $dt = DateTime::createFromFormat('d/m/Y', $value);
        if ($dt === false) {
            // Try ISO as fallback.
            $dt = DateTime::createFromFormat('Y-m-d', $value);
        }
        return $dt ? $dt->format('Y-m-d H:i:s') : null;
    }
}
