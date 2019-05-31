<?php

use common\components\MainFunctions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MeasureType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="measure-type-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php
        if (!$model->isNewRecord) {
            echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
        } else {
            echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
        }
    ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
