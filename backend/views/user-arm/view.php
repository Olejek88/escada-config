<?php
/* @var $model */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Просмотр профиля';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи АРМ'), 'url' => ['index']];
?>
<div class="task-stage-view box-padding">

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
                        <?php
                        echo $this->render('@backend/views/yii2-app/layouts/buttons.php',
                            ['model' => $model]);
                        ?>
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
