<?php

use backend\models\SshForm;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\ActiveForm;

/* @var $this View */
/* @var $model SshForm */
/* @var $dataProvider ArrayDataProvider */

$this->title = 'Ssh';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $gridColumns = [
        [
            'attribute' => 'id',
            'content' => function ($data) {
                return $data['id'];
            }
        ],
        [
            'attribute' => 'cmd',
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'header' => 'Действия',
            'template' => '{delete}',
            'headerOptions' => ['class' => 'kartik-sheet-style'],
        ],
    ];
    ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'ssh',
    ]) ?>
    <?php echo $form->field($model, 'localPort')->input('text') ?>
    <?php echo $form->field($model, 'bindIp')->input('text') ?>
    <?php echo $form->field($model, 'remotePort')->input('text') ?>
    <?php echo $form->field($model, 'remoteHost')->input('text') ?>
    <?php echo $form->field($model, 'user')->input('text') ?>
    <?php echo $form->field($model, 'password')->input('password') ?>
    <?php echo Html::submitButton(Yii::t('app', 'Запустить'), ['class' => 'btn btn-success']); ?>
    <?php ActiveForm::end() ?>

</div>
