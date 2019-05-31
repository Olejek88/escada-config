<?php

namespace backend\controllers;

use backend\models\UserArm;
use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UserArmController extends Controller
{
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
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect('/user');
    }

    public function actionCreate()
    {
        $model = new UserArm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->armUser()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->redirect('index');
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
