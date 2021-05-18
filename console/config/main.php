<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
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
    ],

    'params' => $params,
];
