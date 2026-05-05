<?php
/** Lógica de negocio de Usuarios. Equivalente a BL/BLUsuario.vb. */

require_once __DIR__ . '/../DA/DAUsuario.php';

class BLUsuario
{
    private DAUsuario $da;

    public function __construct()
    {
        $this->da = new DAUsuario();
    }

    public function ConsultarUs(): array
    {
        return $this->da->ConsultarUsuarios();
    }

    public function CargarUsuario(string $cod): array
    {
        return $this->da->CargarUsuario($cod);
    }

    public function UsuariosAsociados(string $ccod_empresa): array
    {
        return $this->da->UsuariosAsociados($ccod_empresa);
    }

    public function InsertarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        return $this->da->InsertarUsuario($obj, $conex);
    }

    public function InsertarUsuarioAdmin(BEUsuario $obj): bool
    {
        return $this->da->InsertarUsuarioAdmin($obj);
    }

    public function EditarUsuarioAdmin(BEUsuario $obj): bool
    {
        return $this->da->EditarUsuarioAdmin($obj);
    }

    public function EditarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        return $this->da->EditarUsuario($obj, $conex);
    }

    public function EliminarUsuarioAdmin(string $cod, BEUser $obj): bool
    {
        return $this->da->EliminarUsuarioAdmin($cod, $obj);
    }

    public function EliminarUsuario(string $usuario, string $ipServidor, string $nomServidor, BEUser $obj): bool
    {
        return $this->da->EliminarUsuario($usuario, $ipServidor, $nomServidor, $obj);
    }

    /** @return array{0:bool,1:string} */
    public function ValidarBDEmpresa(string $ccod_empresa): array
    {
        return $this->da->ValidarBDEmpresa($ccod_empresa);
    }
}
