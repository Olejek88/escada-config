<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'mtm-worker' => [
        // контроллер для запуска "демона" console\workers\MtmAmqpWorker в докере
        'class' => 'console\controllers\MtmWorker',
        'logFile' => 'php://stdout',
    ],
    'daemon' => [
        'class' => 'inpassor\daemon\Controller',
        'uid' => 'daemon',
        'pidDir' => '@console/runtime/daemon',
        'logsDir' => '@console/runtime/daemon/logs',
        'clearLogs' => false,
        'workersMap' => [
            'mtm_amqp_worker' => [
                'class' => 'console\workers\MtmAmqpWorker',
                'active' => true,
                'maxProcesses' => 1,
            ],
        ],
    ],
];
