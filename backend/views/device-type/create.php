<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeviceType */
/* @var $parentModel yii\base\DynamicModel */

$this->title = Yii::t('app', 'Создать тип оборудования');
?>
<div class="order-status-view box-padding" style="width: 95%; min-height: 782px">
    <?php
    echo $this->render('@backend/views/yii2-app/layouts/references-menu.php');
    ?>
    <div class="panel panel-default" style="float: right; width: 75%">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?php echo Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?php echo $this->render(
                            '_form',
                            [
                                'model' => $model
                            ]
                        ) ?>
                    </h6>
                    <?php echo GridView::widget(
                        [
                            'dataProvider' => $dataProvider,
                            'tableOptions' => [
                                'class' => 'table-striped table table-bordered table-hover table-condensed'
                            ],
                            'columns' => [
                                [
                                    'attribute' => 'uuid',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => [
                                        'class' => 'text-center'
                                    ],
                                    'content' => function ($data) {
                                        return $data->uuid;
                                    }
                                ],
                                [
                                    'attribute' => 'title',
                                    'contentOptions' => [
                                        'class' => 'table_class'
                                    ],
                                    'headerOptions' => [
                                        'class' => 'text-center'
                                    ],
                                    'content' => function ($data) {
                                        return $data->title;
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
                                ]
                            ],
                        ]
                    ); ?>
                </div>
            </div>

        </div>
    </div>

</div>
