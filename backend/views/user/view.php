<?php
/* @var $model common\models\User */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->username;
?>

<div class="order-status-view box-padding">

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
                        <?= Html::a(Yii::t('app', 'Обновить'), ['update', 'id' => $model->id],
                            ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Вы действительно хотите удалить данный элемент?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </p>
                    <h6>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'username',
                                'auth_key',
                                'password_hash',
                                'password_reset_token',
                                'email:email',
                                'status',
                                'created_at',
                                'updated_at',
                            ],
                        ]) ?>
                    </h6>
                </div>
            </div>

        </div>
    </div>
</div>
