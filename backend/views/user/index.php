<?php
/* @var $model common\models\User */

/* @var $searchModel backend\models\UserSearch */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Пользователи');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">

                    <p class="text-center">
                        <?= Html::a(Yii::t('app', 'Создать'), ['/user-arm/create'], ['class' => 'btn btn-success']) ?>
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
                                    'attribute' => 'email',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->email;
                                    }
                                ],
                                [
                                    'attribute' => 'username',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->username;
                                    }
                                ],
                                [
                                    'attribute' => 'name',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->name;
                                    }
                                ],
                                [
                                    'attribute' => 'auth_key',
                                    'contentOptions' => [
                                        'class' => 'table_class',
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content' => function ($data) {
                                        return $data->auth_key;
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => 'Действия',
                                    'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                    'contentOptions' => [
                                        'class' => 'text-center',
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
