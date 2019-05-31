<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MeasureSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="measured-value-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'equipment_operation_uuid') ?>

    <?= $form->field($model, 'operation_pattern_step_result') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'value') ?>

    <?php // echo $form->field($model, 'value') ?>

    <?php // echo $form->field($model, 'attempt_send_date') ?>

    <?php // echo $form->field($model, 'attempt_count') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
