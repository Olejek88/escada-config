<?php
/* @var $searchModel backend\models\UsersSearch */

/* @var $dataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Пользователи');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>

            <ul class="nav nav-tabs" style="width: 200px; margin: 0 auto;">
                <li class="active"><a href="/users">Операторы</a></li>
                <li class=""><a href="/user">АРМ</a></li>
            </ul>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?php echo Html::a(
                            Yii::t('app', 'Создать'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>

                    <h6 class="text-center">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'tableOptions' => [
                                'class' => 'table-striped table table-bordered table-hover table-condensed'
                            ],
                            'columns' => [
                                [
                                    'attribute' => '_id',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                        'style' => 'width: 50px; text-align: center'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute' => 'name',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return Html::a(Html::encode($data->name), Url::to(['view', 'id' => $data->_id]));
                                    }
                                ],
                                [
                                    'attribute' => 'pin',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        $data->pin;
                                    }
                                ],
                                [
                                    'attribute' => 'user_id',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->user_id;
                                    }
                                ],
                                [
                                    'attribute' => 'contact',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->contact;
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Действия',
                                    'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                    'contentOptions' => [
                                        'class' => 'text-center'
                                    ],
                                    'template' => '{view} {update} {delete}{link}',
                                ],
                            ],
                        ]); ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
