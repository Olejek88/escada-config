<?php

namespace backend\controllers;

use backend\models\SshForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * AreaController implements the CRUD actions for Area model.
 */
class SshController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Area models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new SshForm();
        if ($request->isPost) {
            if ($model->load($request->post()) && $model->validate()) {
                $cmd = self::getSshpassCmd($model->password, $model->localPort, $model->bindIp, $model->remotePort,
                    $model->user, $model->remoteHost);
                exec($cmd);
                sleep(5);
                return $this->redirect('ssh/index');
            }
        } else {
            $model = new SshForm();
            $model->localPort = 41234;
            $model->bindIp = 'localhost';
            $model->remotePort = 22;
            $model->remoteHost = 'iot.mtm-smart.com';
            $model->user = 'support';
            $model->password = '';
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => self::getSshpassProcesses(),
        ]);

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $password
     * @param $localPort
     * @param $bindIp
     * @param $remotePort
     * @param $user
     * @param $remoteHost
     * @return string
     */
    public static function getSshpassCmd($password, $localPort, $bindIp, $remotePort, $user, $remoteHost)
    {
        $cmd = "/usr/bin/sshpass -p '{$password}' ";
        $cmd .= "/usr/bin/ssh -C -N -R {$localPort}:{$bindIp}:{$remotePort} ";
        $cmd .= "-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null ";
        $cmd .= "{$user}@{$remoteHost} -p {$remotePort} > /dev/null 2>&1 &";
        return $cmd;
    }

    private function getSshpassProcesses()
    {
        $processes = [];
        $cmd = 'ps aux | grep sshpass';
        exec($cmd, $output);
        foreach ($output as $item) {
            $pos = strpos($item, '/usr/bin/sshpass');
            if ($pos !== false) {
                preg_match('/\s(\d+)\s/', $item, $matches);
                $processes[] = ['id' => $matches[1], 'cmd' => substr($item, $pos)];
            }
        }

        return $processes;
    }

    public function actionDelete($id)
    {
        $processes = self::getSshpassProcesses();
        if (!empty($processes[$id])) {
            $cmd = "/bin/kill {$processes[$id]['id']}";
            exec($cmd);
        }

        return $this->redirect('ssh/index');
    }
}
