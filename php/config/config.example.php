<?php
/**
 * Configuracion de la aplicacion DatPosAdmin (PHP + Microsoft SQL Server).
 * Copiar a config.php y ajustar a tu entorno.
 */

return [
    /**
     * Conexion ADMIN: BD maestra DatPosAdmin (usuarios admin, empresas,
     * roles, ubigeo, menus). La usa Database::getAdminConnection() y todos
     * los SPs llamados con Database::selectStored / executeStored.
     *
     * El campo `server` admite varias formas (sintaxis ODBC Driver 18):
     *   - 'localhost'                          -> default instance, puerto 1433
     *   - 'localhost,1433'                     -> host + puerto
     *   - 'localhost\\SQLEXPRESS'              -> instance nombrada (Windows)
     *   - 'tcp:miserver.database.windows.net'  -> Azure SQL
     *
     * Si necesitas auth integrada (Windows Auth) deja `user` y `pass` vacios
     * y agrega `'Trusted_Connection' => 'yes'` en `extra`.
     */
    'db' => [
        'server'  => 'localhost,1433',
        'dbname'  => 'DatPosAdmin',
        'user'    => 'sa',
        'pass'    => '',
        'extra'   => [
            // Cualquier opcion adicional del DSN sqlsrv:
            // 'TrustServerCertificate' => '1',
            // 'Encrypt'                => '0',
            // 'ConnectionPooling'      => '1',
            // 'LoginTimeout'           => '15',
        ],
    ],

    /**
     * Conexion TENANT: credenciales que se usan al abrir conexiones
     * dinamicas a las BDs hijas (cnombre_servidor / cnombre_bd de cada
     * empresa). En el legacy eran U76GY / ADM hardcoded, aqui son
     * configurables. Si todos tus tenants usan el mismo SQL Server, podes
     * dejar el mismo user/pass que admin.
     */
    'tenant' => [
        'user'  => 'U76GY',
        'pass'  => 'ADM',
        'extra' => [
            // 'TrustServerCertificate' => '1',
        ],
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
