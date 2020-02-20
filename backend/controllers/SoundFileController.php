<?php

namespace backend\controllers;

use Yii;
use common\models\SoundFile;
use backend\models\SoundFileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Throwable;

/**
 * SoundFileController implements the CRUD actions for SoundFile model.
 */
class SoundFileController extends Controller
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
     * Lists all SoundFile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SoundFileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SoundFile model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the SoundFile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SoundFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SoundFile::find()->where(['_id' => $id, 'deleted' => 0])->limit(1)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new SoundFile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SoundFile();
        if ($model->load(Yii::$app->request->post())) {
            // process uploaded image file instance
            $uFile = $model->uploadSoundFile();

            if ($model->save()) {
                // upload only if valid uploaded file instance found
                if ($uFile !== false) {
                    $filePath = $model->getSoundFile();
                    if (!file_exists($model->uploadPath)) {
                        mkdir($model->uploadPath, 0777, true);
                    }

                    $uFile->saveAs($filePath);
                }
                return $this->redirect(['view', 'id' => $model->_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SoundFile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        $oldFilePath = $model->getSoundFile();
        $oldFileName = $model->soundFile;

        if ($model->load(Yii::$app->request->post())) {
            // process uploaded image file instance
            $uFile = $model->uploadSoundFile();

            // revert back if no valid file instance uploaded
            if ($uFile === false) {
                $model->soundFile = $oldFileName;
            }

            if ($model->save()) {
                // upload only if valid uploaded file instance found
                if ($uFile !== false && unlink($oldFilePath)) { // delete old and overwrite
                    $path = $model->getSoundFile();
                    $uFile->saveAs($path);
                }
                return $this->redirect(['view', 'id' => $model->_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SoundFile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'delete';
        $model->deleted = true;
        $model->deleteSoundFile();
        $model->save();

        return $this->redirect(['index']);
    }
}
