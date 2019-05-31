<?php

use common\models\Camera;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model Camera */

$this->title = "Камеры";
?>
<div class="order-status-view box-padding">

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
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
                    </p>
                    <h6>
                        <?php echo DetailView::widget(
                            [
                                'model' => $model,
                                'attributes' => [
                                    'uuid',
                                    'title',
                                    [
                                        'label' => 'Статус',
                                        'value' => $model['deviceStatus']->title
                                    ],
                                    [
                                        'label' => 'Контроллер',
                                        'value' => $model['node']->address
                                    ],
                                    [
                                        'label' => 'Объект',
                                        'value' => $model['node']['object']->getAddress()
                                    ],
                                    'createdAt',
                                    'changedAt',
                                ],
                            ]
                        ) ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
