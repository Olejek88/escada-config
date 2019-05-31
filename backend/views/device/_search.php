<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-search box-padding">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'deviceTypeUuid') ?>

    <?php echo $form->field($model, 'deviceStatusUuid') ?>

    <?php echo $form->field($model, 'objectUuid') ?>

    <?php echo $form->field($model, 'serial') ?>

    <?php echo $form->field($model, 'tag') ?>

    <?php echo $form->field($model, 'testDate') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?php
        echo Html::resetButton(
            Yii::t('app', 'Reset'),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
