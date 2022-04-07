<?php

use king\lib\Env;

return [
    'domain' => Env::get('app.domain'),
    'default_folder' => 'www',
    'default_page' => 'test/index',
    'cache_driver' => 'redis',
    'suffix' => '.html',
    'auto_xss' => true,
    'show_error' => Env::get('app.show_error'),
    'error_file' => Env::get('app.error_file'),
    'sentry' => Env::get('sentry.enable', false),
    'log_error' => Env::get('app.log_error'),
    'use_composer'  => true,
    'only_route' => Env::get('app.only_route'),
    'timezone' => 'PRC',
    'valid_path' => 'Common',
    'locale' => 'en',
    'permission' => true,
    'auto_attr' => true,
    'post_json' => false
];
