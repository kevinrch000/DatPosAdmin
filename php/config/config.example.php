<?php
/**
 * Configuracion de la aplicacion DatPosAdmin (PHP).
 * Copiar a config.php y ajustar a tu entorno.
 */

return [
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'dbname'  => 'DatPosAdmin',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
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
