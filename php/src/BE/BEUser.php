<?php
/**
 * Entidad de sesion (usuario logueado). Equivalente a BE/BEUser.vb.
 */

class BEUser
{
    public int $id_usuario = 0;
    public string $ccod_usuario = '';
    public string $cdsc_usuario = '';
    public string $id_rol = '';
    public string $ccod_empresa = '';
    public string $cdsc_empresa = '';
    public string $cnombre_bd = '';
    public string $cnombre_servidor = '';
}
