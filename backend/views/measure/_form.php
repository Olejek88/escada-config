<?php

use common\components\MainFunctions;
use common\models\Device;
use common\models\MeasureType;
use common\models\SensorChannel;
use common\models\User;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Measure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="measured-value-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-11',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php

    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?php
    $sensorChannels = SensorChannel::find()->all();
    $items = ArrayHelper::map($sensorChannels, 'uuid', function ($data) {
        return $data['device']->getFullTitle().' ['.$data['title'].']';
    });
    echo $form->field($model, 'sensorChannelUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите канал измерения..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>
    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 200px; margin-bottom: 0;">Дата измерения</p>
        <?= DateTimePicker::widget([
            'model' => $model,
            'attribute' => 'date',
            'language' => 'ru',
            'size' => 'ms',
            'clientOptions' => [
                'autoclose' => true,
                'linkFormat' => 'yyyy-mm-dd H:ii:ss',
                'todayBtn' => true
            ]
        ]);
        ?>
    </div>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::ORGANISATION_UUID])->label(false); ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
