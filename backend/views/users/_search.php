<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'login') ?>

    <?= $form->field($model, 'pass') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'tagid') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'whois') ?>

    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'connectiondate') ?>

    <?php // echo $form->field($model, 'createdAt') ?>

    <?php // echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
