<?php

namespace console\controllers;

use console\workers\MtmAmqpWorker;
use yii\console\Controller;

class MtmWorker extends Controller
{
    public function actionIndex()
    {
        $worker = new MtmAmqpWorker();
        $worker->init();
        $worker->run();
    }
}
