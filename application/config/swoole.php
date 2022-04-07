<?php

return [
    'server' => [
        'process_name' => 'Kapi',
        'host' => '0.0.0.0',
        'port' => 9505,
        'server_type' => 'socket', // 可选tcp,http,socket
        'server_mode' => SWOOLE_PROCESS,
        'socket_type' => SWOOLE_TCP,
        'option' => [
            'daemonize' => true,
            'pid_file' => APP_PATH . 'log/swoole.pid',
            'log_file' => APP_PATH . 'log/swoole.log',
            'reactor_num' => swoole_cpu_num(),
            'worker_num' => swoole_cpu_num(),
            'task_worker_num' => swoole_cpu_num(),
            'package_max_length' => 20 * 1024 * 1024,
            'buffer_output_size' => 10 * 1024 * 1024,
            'socket_buffer_size' => 128 * 1024 * 1024,
            'task_enable_coroutine' => true
        ],
    ],
    'websocket' => [
        'handle' => 'app\\controller\\www\\WebSocket',
        'ping_interval' => 25000,
        'ping_timeout' => 60000,
        'room' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'max_active' => 3,
            'max_wait_time' => 5,
        ],
        'listen' => [],
        'subscribe' => [],
    ],
    'process' => [
        ['handle' => 'app\\controller\\www\\test', 'function' => 'index', 'num' => '2', 'daemon' => true, 'coroutine' => true, 'run' => true],
    ]
];
