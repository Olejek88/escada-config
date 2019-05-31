<?php

use common\components\MainFunctions;
use common\models\DeviceStatus;
use common\models\DeviceType;
use common\models\Node;
use common\models\Threads;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Device */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'form-input-documentation',
            'options' => [
                'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
                'enctype' => 'multipart/form-data'
            ],
        ]
    ); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>
    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'port')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'speed')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'message')->hiddenInput(['value' => ''])->label(false); ?>
    <?php echo $form->field($model, 'status')->hiddenInput(['value' => 0])->label(false); ?>
    <?php echo $form->field($model, 'work')->hiddenInput(['value' => 0])->label(false); ?>

    <?php
    $deviceType = DeviceType::find()->all();
    $items = ArrayHelper::map($deviceType, 'uuid', 'title');
    echo $form->field($model, 'deviceTypeUuid',
        ['template' => MainFunctions::getAddButton("/device-type/create")])->widget(Select2::class,
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

    <div class="form-group text-center">

        <?php
        if ($model->isNewRecord) {
            $buttonText = Yii::t('app', 'Создать');
            $buttonClass = 'btn btn-success';
        } else {
            $buttonText = Yii::t('app', 'Обновить');
            $buttonClass = 'btn btn-primary';
        }

        echo Html::submitButton($buttonText, ['class' => $buttonClass]);
        ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
