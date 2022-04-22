<?php

namespace console\controllers;

use console\workers\MtmAmqpWorker;
use yii\console\Controller;

class MtmWorker extends Controller
{
    public $logFile;

    public function options($actionID)
    {
        return ['logFile'];
    }

    public function actionIndex()
    {
        $options = [
            'logFile' => $this->logFile != null ? $this->logFile : 'php://stdout',
        ];
        $worker = new MtmAmqpWorker($options);
        $worker->init();
        $worker->run();
    }
}
