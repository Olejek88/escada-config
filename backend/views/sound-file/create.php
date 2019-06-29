<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SoundFile */

$this->title = 'Create Sound File';
$this->params['breadcrumbs'][] = ['label' => 'Sound Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sound-file-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
