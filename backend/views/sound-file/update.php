<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SoundFile */

$this->title = 'Update Sound File: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Sound Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sound-file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
