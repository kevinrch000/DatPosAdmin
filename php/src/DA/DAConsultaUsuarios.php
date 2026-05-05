<?php
/** Acceso a datos para consultas de usuarios. Equivalente a DA/DAConsultaUsuarios.vb. */

require_once __DIR__ . '/../Db.php';

class DAConsultaUsuarios
{
    /** @return array<int,array<string,mixed>> */
    public function ConsultasUsuariosPrincipal(string $codigo, string $estado): array
    {
        return Db::callSp('webDatpos_consultaPorCodEmpresa', [$codigo, $estado]);
    }

    /** @return array<int,array<string,mixed>> */
    public function ConsultaUsuariosPorEmpresa(string $empresa): array
    {
        return Db::callSp('webDatpos_countUsuariosPorEmpresa', [$empresa]);
    }
}
