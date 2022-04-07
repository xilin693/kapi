<?php

use king\lib\Env;

return [
    'default' => [
        'user' => Env::get('database.user'),
        'password' => Env::get('database.password'),
        'host' => Env::get('database.host'),
        'db' => Env::get('database.db'),
        'prefix' => Env::get('database.prefix'),
        'charset' => Env::get('database.charset'),
        'size' => 50
    ],
    'rest' => [
        'user' => Env::get('rest.user'),
        'password' => Env::get('rest.password'),
        'host' => Env::get('rest.host'),
        'db' => Env::get('rest.db'),
        'prefix' => Env::get('rest.prefix'),
        'charset' => Env::get('rest.charset'),
        'size' => 50
    ]
];
