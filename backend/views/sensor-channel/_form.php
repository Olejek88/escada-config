<?php

use common\components\MainFunctions;
use common\models\MeasureType;
use common\models\Organisation;
use common\models\Objects;
use common\models\SensorChannel;
use common\models\Task;
use common\models\User;
use common\models\Users;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Device;
use common\models\requestStatus;

/* @var $this yii\web\View */
/* @var $model common\models\SensorChannel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-request-form" style="min-height: 680px">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    $type = MeasureType::find()->all();
    $items = ArrayHelper::map($type, 'uuid', 'title');
    echo $form->field($model, 'measureTypeUuid',
        ['template' => MainFunctions::getAddButton("/measure-type/create")])->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите тип..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <?php
    $device = Device::find()->all();
    $items = ArrayHelper::map($device, 'uuid', function ($data) {
        return $data->getFullTitle();
    });
    echo $form->field($model, 'deviceUuid',
        ['template' => MainFunctions::getAddButton("/device/create")])->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите оборудование..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::ORGANISATION_UUID])->label(false); ?>
    <?= $form->field($model, 'register')->textInput() ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
