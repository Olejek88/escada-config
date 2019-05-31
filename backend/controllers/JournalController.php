<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class JournalController extends Controller
{
    public function init()
    {

        if (Yii::$app->getUser()->isGuest) {
            throw new UnauthorizedHttpException();
        }

    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
