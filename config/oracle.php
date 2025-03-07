<?php

return [
    'oracle' => [
            'driver'         => 'oracle',
            'tns'            => env('DB_ORACLE_TNS', ''),
            'host'           => env('DB_ORACLE_HOST', '127.0.0.1'),
            'port'           => env('DB_ORACLE_PORT', '1521'),
            'database'       => env('DB_ORACLE_DATABASE', ''),
            'service_name'   => env('DB_ORACLE_SERVICE_NAME', 'forge'),
            'username'       => env('DB_ORACLE_USERNAME', 'forge'),
            'password'       => env('DB_ORACLE_PASSWORD', ''),
            'charset'        => env('DB_ORACLE_CHARSET', 'AL32UTF8'),
            'prefix'         => env('DB_ORACLE_PREFIX', ''),
            'prefix_schema'  => env('DB_ORACLE_SCHEMA_PREFIX', ''),
            'server_version' => env('DB_ORACLE_SERVER_VERSION', '12c'),
    ],
];
