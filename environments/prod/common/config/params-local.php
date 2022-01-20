<?php
return [
    'amqpServer' => [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'mtm',
        'password' => '',

    ],
    'node' => [
        'oid' => 0,
        'nid' => 0,
    ],
    'videoServer' => [
        'host' => 'localhost',
        'port' => 1935,
        'app' => 'lightcams',
        'publishTime' => '300',
    ],
    'apiServer' => 'http://localhost',
    'fileServer' => 'http://localhost',
];
