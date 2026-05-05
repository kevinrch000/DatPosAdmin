<?php
/**
 * Entidad Usuario. Equivalente a BE/BEUsuario.vb.
 */

class BEUsuario
{
    public int $id_usuario = 0;
    public string $ccod_usuario = '';
    public string $cdsc_usuario = '';
    public string $cpassw = '';
    public string $cdirec = '';
    public string $id_rol = '';
    public string $cdsc_rol = '';
    public string $ccod_empresa = '';
    public string $empresa = '';
    public int $id_estado = 0;
    public string $cstatus = '';
    public string $cnombre_bd = '';
    public string $cnombre_servidor = '';
    public string $cmail = '';
    public string $ctelf = '';
    public string $ccelular = '';
    public string $dfch_crea = '';

    /**
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
