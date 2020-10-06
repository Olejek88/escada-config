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
                $cmd = "/usr/bin/sshpass -p '{$model->password}' ";
                $cmd .= "/usr/bin/ssh -C -N -R {$model->localPort}:{$model->bindIp}:{$model->remotePort} ";
                $cmd .= "-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null ";
                $cmd .= "{$model->user}@{$model->remoteHost} -p {$model->remotePort} > /dev/null 2>&1 &";
                exec($cmd);
                sleep(5);
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

        return Yii::$app->response->redirect('index');
    }
}
