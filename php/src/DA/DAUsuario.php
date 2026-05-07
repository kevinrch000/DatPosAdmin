<?php
/**
 * Acceso a datos para Usuarios. Equivalente a DA/DAUsuario.vb.
 */

require_once __DIR__ . '/../Db.php';
require_once __DIR__ . '/../Database.php';
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
                    ISNULL(U.cdirec, '')   AS cdirec,
                    ISNULL(R.cdsc_rol, '') AS cdsc_rol,
                    ISNULL(U.ccelular, '') AS ccelular,
                    ISNULL(U.cmail, '')    AS cmail,
                    CAST(U.id_estado AS VARCHAR(10)) AS id_estado
                FROM dbo.Usuarios U
                LEFT JOIN dbo.Roles R ON R.id_rol = U.id_rol
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
            self::hashPassword($obj->cpassw),
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
        // Si la pantalla envia password vacio, mandamos string vacio: el SP
        // lo interpreta como "no cambiar" y conserva el hash actual.
        return Db::execSp('webDatpos_editarUsuarioAdmin', [
            $obj->ccod_usuario,
            $obj->cdsc_usuario,
            self::hashPassword($obj->cpassw),
            $obj->cdirec,
            (int)$obj->id_rol,
            $obj->ccod_empresa,
            $obj->cstatus,
            $obj->cmail,
            $obj->ctelf,
            $obj->ccelular,
        ]);
    }

    /**
     * Hashea con bcrypt si hay password; si llega vacio, devuelve string vacio
     * (los SPs de admin interpretan vacio como "no cambiar").
     */
    private static function hashPassword(string $plain): string
    {
        if ($plain === '') {
            return '';
        }
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    public function EliminarUsuarioAdmin(string $cod, ?BEUser $obj = null): bool
    {
        return Db::execSp('webDatpos_eliminarUsuarioAdmin', [$cod]);
    }

    /**
     * Inserta el usuario en la BD HIJA de la empresa (multi-tenant).
     * En el original llamaba a `webDatpos_insertarUsuario` via OtraConexion.
     * Aqui usamos `Database::executeStoredTenant` que abre conexion dinamica
     * a `cnombre_servidor`/`cnombre_bd` de la empresa destino.
     *
     * Si la BD hija no esta configurada (server/dbname vacios), solo se
     * mantiene la insercion en DatPosAdmin (BD admin) y se reporta exito.
     *
     * @return array{0:bool,1:string,2:string,3:string}
     */
    public function InsertarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        if (!$this->existeEmpresa($obj->ccod_empresa)) {
            return [false, 'ERROR', "La empresa '{$obj->ccod_empresa}' no esta configurada.", ''];
        }
        $tenant = $this->tenantFromEmpresa($obj->ccod_empresa);
        if ($tenant === null) {
            return [true, '0', '', (string)$obj->id_rol];
        }
        $ok = Database::executeStoredTenant('webDatpos_insertarUsuario', [
            $obj->ccod_usuario,
            $obj->cpassw,
            $obj->cdsc_usuario,
            (int)$obj->id_rol,
            $obj->cmail,
            $obj->ctelf,
            $obj->ccelular,
            $obj->cdirec,
        ], $tenant);
        return $ok
            ? [true, '0', '', (string)$obj->id_rol]
            : [false, 'ERROR', "No se pudo insertar el usuario en la BD hija ({$tenant->cnombre_bd}).", ''];
    }

    /**
     * @return array{0:bool,1:string,2:string,3:string}
     */
    public function EditarUsuario(BEUsuario $obj, BEUser $conex): array
    {
        if (!$this->existeEmpresa($obj->ccod_empresa)) {
            return [false, 'ERROR', "La empresa '{$obj->ccod_empresa}' no esta configurada.", ''];
        }
        $tenant = $this->tenantFromEmpresa($obj->ccod_empresa);
        if ($tenant === null) {
            return [true, '0', '', (string)$obj->id_rol];
        }
        $ok = Database::executeStoredTenant('webDatpos_editarUsuario', [
            $obj->ccod_usuario,
            $obj->cpassw,
            $obj->cdsc_usuario,
            (int)$obj->id_rol,
            $obj->cmail,
            $obj->ctelf,
            $obj->ccelular,
            $obj->cdirec,
        ], $tenant);
        return $ok
            ? [true, '0', '', (string)$obj->id_rol]
            : [false, 'ERROR', "No se pudo editar el usuario en la BD hija ({$tenant->cnombre_bd}).", ''];
    }

    public function EliminarUsuario(string $usuario, string $ipServidor, string $nomServidor, BEUser $conex): bool
    {
        // El admin (id_estado=0) ya se marca en EliminarUsuarioAdmin. Si la
        // empresa tiene BD hija, replicamos el soft-delete ahi tambien.
        if ($ipServidor === '' || $nomServidor === '') {
            return true;
        }
        $tenant = (object)[
            'cnombre_servidor' => $ipServidor,
            'cnombre_bd'       => $nomServidor,
        ];
        return Database::executeStoredTenant('webDatpos_eliminarUsuario', [$usuario], $tenant);
    }

    /**
     * Construye un "tenant" minimal (server/dbname) a partir del codigo de
     * empresa, leyendo Empresas.cnombre_servidor y Empresas.cnombre_bd.
     */
    private function tenantFromEmpresa(string $ccod_empresa): ?object
    {
        $stmt = Db::pdo()->prepare(
            "SELECT ISNULL(cnombre_servidor, '') AS cnombre_servidor,
                    ISNULL(cnombre_bd, '')       AS cnombre_bd
             FROM dbo.Empresas WHERE ccod_empresa = ?"
        );
        $stmt->execute([$ccod_empresa]);
        $row = $stmt->fetch();
        if (!$row || $row['cnombre_servidor'] === '' || $row['cnombre_bd'] === '') {
            return null;
        }
        return (object)$row;
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
            "SELECT cnombre_bd FROM dbo.Empresas WHERE ccod_empresa = ? AND id_estado = 1"
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
                       ISNULL(cnombre_servidor, '') AS cnombre_servidor,
                       ISNULL(cnombre_bd, '')       AS cnombre_bd
                FROM dbo.Empresas
                WHERE id_estado = 1
                  AND ISNULL(cnombre_bd, '') <> ''";
        $stmt = Db::pdo()->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    private function existeEmpresa(string $ccod_empresa): bool
    {
        $stmt = Db::pdo()->prepare(
            "SELECT 1 FROM dbo.Empresas WHERE ccod_empresa = ? AND id_estado = 1"
        );
        $stmt->execute([$ccod_empresa]);
        return (bool)$stmt->fetchColumn();
    }
}
