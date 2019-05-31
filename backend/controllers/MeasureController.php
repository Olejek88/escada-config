<?php

namespace backend\controllers;

use backend\models\MeasureSearch;
use common\models\Measure;
use Yii;
use yii\db\StaleObjectException as StaleObjectExceptionAlias;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * MeasureController implements the CRUD actions for Measure model.
 */
class MeasureController extends Controller
{
    /**
     * @inheritdoc
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
     * @throws UnauthorizedHttpException
     */
    public function init()
    {

        if (Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    /**
     * Lists all Measure models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MeasureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 200;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Measure model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Measure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Measure();
        if ($model->load(Yii::$app->request->post())) {
            if ($model['sensorChannel'])
                $model['measureTypeUuid'] = $model['sensorChannel']['measureTypeUuid'];
            if ($model->save())
                return self::actionIndex();
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a trend of value
     * @return mixed
     */
    public function actionTrend()
    {
        $measure = array(0);
        $name = '';
        if ($_GET["equipment"]) {
            $measure = Measure::find()
                ->where(['sensorChannelUuid' => $_GET["sensorChannel"]])
                ->orderBy('date')
                ->all();
            if ($measure[0] != null)
                $name = $measure[0]['equipment']['equipmentType']->title;
        }
        return $this->render('trend', [
            'values' => $measure,
            'name' => $name
        ]);
    }

    /**
     * Updates an existing Measure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Measure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectExceptionAlias
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Measure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Measure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Measure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
