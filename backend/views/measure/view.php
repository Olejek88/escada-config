<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Measure */

$this->title = 'Измеренное значение';
?>
<div class="box box-default">
    <div class="box-header with-border">
        <h2><?= Html::encode($this->title) ?></h2>
        <div class="box-tools pull-right">
            <span class="label label-default"></span>
        </div>
    </div>
    <div class="box-body" style="padding: 30px;">
        <p class="text-center">
            <?php
            echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                ['model' => $model]);
            ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                '_id',
                'uuid',
                [
                    'label' => 'Канал',
                    'value' => $model['sensorChannel']->title
                ],
                'date',
                'value',
                'createdAt',
                'changedAt',
            ],
        ]) ?>
    </div>
</div>
