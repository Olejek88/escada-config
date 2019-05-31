<?php

use backend\models\SensorChannelSearch;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $searchModel SensorChannelSearch */

$this->title = Yii::t('app', 'Каналы измерения');
?>
<div class="orders-index box-padding-index">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
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
                                    'attribute'=>'_id',
                                    'contentOptions' =>[
                                        'class' => 'table_class',
                                        'style'=>'width: 50px; text-align: center; padding: 5px 10px 5px 10px;'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'content'=>function($data){
                                        return $data->_id;
                                    }
                                ],
                                [
                                    'attribute'=>'device',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => function ($data) {
                                        return $data['device']->getFullTitle();
                                    },
                                ],
                                [
                                    'attribute'=>'measureType',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'measureType.title',
                                ],
                                [
                                    'attribute'=>'register',
                                    'contentOptions' =>[
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => ['class' => 'text-center'],
                                    'value'     => 'register',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header'=>'Действия',
                                    'headerOptions' => ['class' => 'text-center','width' => '70'],
                                    'contentOptions' =>[
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
