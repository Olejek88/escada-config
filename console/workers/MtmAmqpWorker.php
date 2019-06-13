<?php

namespace console\workers;

use inpassor\daemon\Worker;

class MtmAmqpWorker extends Worker
{
    public $active = true;
    public $maxProcesses = 1;
    public $delay = 60;
    public $run = true;

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/mtm_amqp_worker.log';
        parent::init();
    }


    public function run()
    {
        while ($this->run) {
            $this->log("tick...");
            sleep(1);
        }
    }

}