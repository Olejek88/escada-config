<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SoundFileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sound Files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sound-file-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sound File', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            '_id',
            'uuid',
            'oid',
            'title',
            'soundFile',
            //'nodeUuid',
            'deleted',
            //'createdAt',
            //'changedAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
