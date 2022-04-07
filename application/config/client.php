<?php

use king\lib\Env;

return [
    'host' => '0.0.0.0',
    'port' => 9501,
    'timeout' => 3,
    'mode' => 'sync', // 默认为服务端同步传输,如果是异步,请使用async
];