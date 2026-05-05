<?php
/**
 * Acceso a datos para Usuarios. Equivalente a DA/DAUsuario.vb.
 */

require_once __DIR__ . '/../Db.php';
require_once __DIR__ . '/../BE/BEUsuario.php';
require_once __DIR__ . '/../BE/BEUser.php';

class DAUsuario
{
    /** @return array<int,array<string,mixed>> */
    public function CargarUsuario(string $cod): array
    {
        return Db::callSp('webDatpos_consultaUsuario', [$cod]);
    }

    /**
     * UsuariosAsociados se basaba en un SP `webDatpos_usuariosAsociados` que no
     * forma parte del .sql original. Lo emulamos con un SELECT directo a la
     * misma estructura para mantener el endpoint operativo.
     *
     * @return array<int,array<string,mixed>>
     */
    public function UsuariosAsociados(string $ccod_empresa): array
    {
        $sql = "SELECT
                    U.ccod_usuario,
                    U.cdsc_usuario,
                    IFNULL(U.cdirec, '')   AS cdirec,
                    IFNULL(R.cdsc_rol, '') AS cdsc_rol,
                    IFNULL(U.ccelular, '') AS ccelular,
                    IFNULL(U.cmail, '')    AS cmail,
                    CAST(U.id_estado AS CHAR(10)) AS id_estado
                FROM Usuarios U
                LEFT JOIN Roles R ON R.id_rol = U.id_rol
                WHERE U.ccod_empresa = ?";
        $stmt = Db::pdo()->prepare($sql);
        $stmt->execute([$ccod_empresa]);
        return $stmt->fetchAll() ?: [];
    }

    /** @return array<int,array<string,mixed>> */
    public function ConsultarUsuarios(): array
    {
        return Db::callSp('webDatpos_consultaUsuarios');
    }

    public function InsertarUsuarioAdmin(BEUsuario $obj): bool
    {
        return Db::execSp('webDatpos_insertarUsuarioAdmin', [
            $obj->ccod_usuario,
            $obj->cdsc_usuario,
            $obj->cpassw,
            $obj->cdirec,
            (int)$obj->id_rol,
            $obj->ccod_empresa,
            $obj->cstatus,
            $obj->cmail,
            $obj->ctelf,
            $obj->ccelular,
        ]);
    }

    public function EditarUsuarioAdmin(BEUsuario $obj): bool
    {
        return Db::execSp('webDatpos_editarUsuarioAdmin', [
            $obj->ccod_usuario,
            $obj->cdsc_usuario,
            $obj->cpassw,
            $obj->cdirec,
            (int)$obj->id_rol,
            $obj->ccod_empresa,
            $obj->cstatus,
            $obj->cmail,
            $obj->ctelf,
            $obj->ccelular,
        ]);
    }

    public function EliminarUsuarioAdmin(string $cod, ?BEUser $obj = null): bool
    {
        return Db::execSp('webDatpos_eliminarUsuarioAdmin', [$cod]);
    }

    /**
     * En el original, `InsertarUsuario` abria una conexion a la BD hija de la
     * empresa (cnombre_bd) y llamaba a `webDatpos_insertarUsuario` ahi. En esta
     * migracion solo administramos la BD principal (DatPosAdmin). Mantenemos
     * la firma y devolvemos el formato `[ok, errNumber, errMessage, id_rol]`
     * que el JS espera.
     *
     * @return array{0:bool,1:string,2:string,3:string}
     */
    public function InsertarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        if (!$this->existeEmpresa($obj->ccod_empresa)) {
            return [false, 'ERROR', "La empresa '{$obj->ccod_empresa}' no esta configurada.", ''];
        }
        return [true, '0', '', (string)$obj->id_rol];
    }

    /**
     * @return array{0:bool,1:string,2:string,3:string}
     */
    public function EditarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        if (!$this->existeEmpresa($obj->ccod_empresa)) {
            return [false, 'ERROR', "La empresa '{$obj->ccod_empresa}' no esta configurada.", ''];
        }
        return [true, '0', '', (string)$obj->id_rol];
    }

    public function EliminarUsuario(string $usuario, string $ipServidor, string $nomServidor, BEUser $conex): bool
    {
        // En el original llama a un SP de la BD hija; aqui ya marcamos
        // id_estado=0 via EliminarUsuarioAdmin.
        return true;
    }

    /**
     * Valida que la empresa exista en DatPosAdmin con id_estado activo.
     * Devuelve [ok, mensaje].
     *
     * @return array{0:bool,1:string}
     */
    public function ValidarBDEmpresa(string $ccod_empresa): array
    {
        $stmt = Db::pdo()->prepare(
            "SELECT cnombre_bd FROM Empresas WHERE ccod_empresa = ? AND id_estado = 1"
        );
        $stmt->execute([$ccod_empresa]);
        $row = $stmt->fetch();
        if (!$row) {
            return [false, "La empresa '$ccod_empresa' no existe o esta inactiva."];
        }
        return [true, ''];
    }

    /**
     * Lista las empresas con BD configurada. Versi&oacute;n simplificada de la
     * original (que consultaba sys.databases): aqui filtramos por
     * cnombre_bd no vacio.
     *
     * @return array<int,array<string,mixed>>
     */
    public function ConsultarEmpresasConBDValida(): array
    {
        $sql = "SELECT id_empresa, ccod_empresa, cdsc_empresa,
                       IFNULL(cnombre_servidor, '') AS cnombre_servidor,
                       IFNULL(cnombre_bd, '')       AS cnombre_bd
                FROM Empresas
                WHERE id_estado = 1
                  AND IFNULL(cnombre_bd, '') <> ''";
        $stmt = Db::pdo()->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    private function existeEmpresa(string $ccod_empresa): bool
    {
        $stmt = Db::pdo()->prepare(
            "SELECT 1 FROM Empresas WHERE ccod_empresa = ? AND id_estado = 1"
        );
        $stmt->execute([$ccod_empresa]);
        return (bool)$stmt->fetchColumn();
    }
}
