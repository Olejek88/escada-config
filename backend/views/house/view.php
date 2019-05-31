<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\House */

$this->title = $model['street']->title . ', ' . $model['number'];
?>
<div class="task-view box-padding">

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
                        <?php echo DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                '_id',
                                'uuid',
                                'number',
                                'street.title',
                                'houseType.title',
                                'createdAt',
                                'changedAt',
                            ],
                        ]) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
