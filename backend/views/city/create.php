<?php
/* @var $model common\models\City */

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Создать город');
?>
<div class="task-create box-padding">

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #fff;">
            <h3 class="text-center" style="color: #333;">
                <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="panel-body">

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade active in" id="list">
                    <h6>
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </h6>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => [
                            'class' => 'table-striped table table-bordered table-hover table-condensed'
                        ],
                        'columns' => [
                            [
                                'attribute'=>'_id',
                                'contentOptions' =>[
                                    'class' => 'table_class',
                                    'style'=>'width: 50px; text-align: center'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'content'=>function($data){
                                    return $data->_id;
                                }
                            ],
                            [
                                'attribute'=>'title',
                                'contentOptions' =>[
                                    'class' => 'table_class'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                                'content'=>function($data){
                                    return $data->title;
                                }
                            ]
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

</div>
