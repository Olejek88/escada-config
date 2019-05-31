<?php

use common\components\MainFunctions;
use common\models\House;
use common\models\SensorChannel;
use common\models\User;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SensorConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>
    <?php
    $sensorChannel = SensorChannel::find()->all();
    $items = ArrayHelper::map($sensorChannel, 'uuid', 'title');
    echo $form->field($model, 'sensorChannelUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите канал'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::ORGANISATION_UUID])->label(false); ?>

    <?= $form->field($model, 'config')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
