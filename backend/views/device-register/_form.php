<?php

use common\components\MainFunctions;
use common\models\Device;
use common\models\User;
use common\models\Users;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeviceRegister */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

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
    <?php
    $equipment = Device::find()->orderBy("_id")->all();
    $items = ArrayHelper::map($equipment, 'uuid', 'address');
    echo $form->field($model, 'deviceUuid')->dropDownList($items);
    ?>

    <div class="pole-mg" style="margin: 0 -15px 20px -15px;">
        <p style="width: 0; margin-bottom: 0;">Дата</p>
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

    <?php
    echo $form->field($model, 'description')->textarea(
        ['rows' => 4, 'style' => 'resize: none;']
    );
    ?>
    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::ORGANISATION_UUID])->label(false); ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
