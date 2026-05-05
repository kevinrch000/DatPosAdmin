<?php
/**
 * Configuracion de la aplicacion DatPosAdmin (PHP).
 * Copiar a config.php y ajustar a tu entorno.
 */

return [
    /**
     * Conexion ADMIN: BD maestra DatPosAdmin (usuarios admin, empresas,
     * roles, ubigeo, menus). La usa Database::getAdminConnection() y todos
     * los SPs llamados con Database::selectStored / executeStored.
     */
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'dbname'  => 'DatPosAdmin',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    /**
     * Conexion TENANT: credenciales que se usan al abrir conexiones
     * dinamicas a las BDs hijas (cnombre_servidor / cnombre_bd de cada
     * empresa). En el legacy SQL Server eran U76GY / ADM hardcoded, aqui
     * son configurables. Si todos tus tenants usan el mismo MySQL, podes
     * dejar el mismo user/pass que admin.
     */
    'tenant' => [
        'port' => 3306,
        'user' => 'datpos_tenant',
        'pass' => '',
    ],

    /**
     * Ruta base publica donde corre la app (Site.layout.php construye URLs
     * absolutas a partir de aqui). En desarrollo con `php -S` dejalo en ''.
     * En produccion bajo Apache/Nginx, ej: '/datposadmin'.
     */
    'base_path' => '',

    /**
     * Tiempo maximo de sesion en segundos (equivalente al sessionState
     * timeout="60" del Web.config original).
     */
    'session_timeout' => 60 * 60,
];
