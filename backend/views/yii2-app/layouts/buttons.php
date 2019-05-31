<?php

use yii\helpers\Html;

/* @var $model */

echo Html::a(Yii::t('app', 'Список'),
    ['index'],
    ['class' => 'btn btn-info']);
?>

<?php
echo Html::a(
    Yii::t('app', 'Обновить'),
    ['update', 'id' => $model['_id']],
    ['class' => 'btn btn-primary']
);
?>

<?php
$msg = 'Вы действительно хотите удалить данный элемент?';
echo Html::a(
    Yii::t('app', 'Удалить'),
    ['delete', 'id' => $model['_id']],
    [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => Yii::t('app', $msg),
            'method' => 'post',
        ],
    ]
) ?>

<?php
echo Html::a(Yii::t('app', 'Добавить'),
    ['create'],
    ['class' => 'btn btn-success']);
?>
