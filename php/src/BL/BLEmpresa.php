<?php
/** Lógica de negocio de Empresas. Equivalente a BL/BLEmpresa.vb. */

require_once __DIR__ . '/../DA/DAEmpresa.php';
require_once __DIR__ . '/../DA/DAUsuario.php';

class BLEmpresa
{
    private DAEmpresa $da;

    public function __construct()
    {
        $this->da = new DAEmpresa();
    }

    public function CargarDepartamento(?string $ccod_cia = null): array
    {
        return $this->da->CargarDepartamento($ccod_cia);
    }

    public function CargarProvincia(string $id_departamento): array
    {
        return $this->da->CargarProvincia($id_departamento);
    }

    public function CargarDistrito(string $id_provincia): array
    {
        return $this->da->CargarDistrito($id_provincia);
    }

    public function CargarCompania(string $cod): array
    {
        return $this->da->CargarCompania($cod);
    }

    public function CargarCompanias(): array
    {
        return $this->da->CargarCompanias();
    }

    public function InsertarCompania(BEEmpresa $obj): bool
    {
        return $this->da->InsertarCompania($obj);
    }

    public function EditarCompania(BEEmpresa $obj): bool
    {
        return $this->da->EditarCompania($obj);
    }

    public function EliminarEmpresa(string $cod): bool
    {
        return $this->da->EliminarEmpresa($cod);
    }

    public function CargarCompaniasConBDValida(): array
    {
        return (new DAUsuario())->ConsultarEmpresasConBDValida();
    }
}
