<?php
/**
 * Entidad Empresa. Equivalente a BE/BEEmpresa.vb.
 */

class BEEmpresa
{
    public int $id_empresa = 0;
    public string $ccod_empresa = '';
    public string $cdescripcion = '';
    public string $cdoc = '';
    public string $cnum_tribu = '';
    public string $cnombre_servidor = '';
    public string $cnombre_bd = '';
    public string $cid_tributario = '';
    public string $cpais_origen = '';
    public string $csimbolo_moneda = '';
    public int $id_tipodocumento = 0;
    public string $cnombre_moneda = '';
    public string $ctarifas = '';
    public string $countUsuarios = '';
    public string $cstatus = '';
    public int $nusuario_extra = 0;
    public int $ntienda_extra = 0;
    public string $dfch_crea = '';
    public string $cdsc_facturador = '';
    public string $ctip_facturador = '';
    public string $cdepartamento = '';
    public string $cdistrito = '';
    public string $cprovincia = '';
    public string $curbanizacion = '';
    public string $cdomicilio = '';
    public string $cubigeo = '';
    public string $nenviosunat = '';
    public string $dfch_sunat = '';
    public string $ccod_cliente_emis = '';
    public string $dfch_vencimiento = '';
    public string $ctoken = '';

    /**
     * Crea una BEEmpresa desde un array asociativo (POST/JSON), tolerando
     * llaves ausentes.
     *
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $obj = new self();
        foreach ($data as $key => $value) {
            if (property_exists($obj, $key)) {
                if ($value === null) {
                    continue;
                }
                $reflection = new ReflectionProperty($obj, $key);
                $type = $reflection->getType();
                if ($type instanceof ReflectionNamedType) {
                    settype($value, $type->getName());
                }
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}
