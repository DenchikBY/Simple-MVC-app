<?php

return [

    'short_response' => false,

    'db' => [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql', //mysql, pgsql
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'database' => 'mvc'
            ]
        ]
    ]

];
