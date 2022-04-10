<?php

use king\lib\Env;

return [
    'dsn' => Env::get('sentry.dsn', 'http://xxx'),
];
