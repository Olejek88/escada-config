<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ObjectSearchType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stage-status-search box-padding">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get',]); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'createdAt') ?>

    <?php echo $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?php echo Html::resetButton(
            Yii::t('app', 'Reset'),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
