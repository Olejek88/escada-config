<?php

use common\models\EquipmentStatus;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-status-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options' => [
            'id'      => 'form'
        ],
    ]);
    ?>

    <?php
        echo $form->field($model, '_id')->hiddenInput(['value' => $model["_id"]])->label(false);
        $equipmentStatus = EquipmentStatus::find()->all();
        $items = ArrayHelper::map($equipmentStatus, 'uuid', 'title');
        echo $form->field($model, 'equipmentStatusUuid')->widget(Select2::class,
        [
            'name' => 'status',
            'language' => 'ru',
            'value' => $model["equipmentStatus"]["title"],
            'data' => $items,
            'options' => ['placeholder' => 'Выберите статус ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Сменить') : Yii::t('app', 'Сменить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>
    <script>
        $(document).on("beforeSubmit", "#dynamic-form", function () {
        }).on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: "status",
                type: "post",
                data: $('form').serialize(),
                success: function () {
                    $('#modalStatus').modal('hide');
                },
                error: function () {
                }
            })
        });
    </script>

    <?php ActiveForm::end(); ?>

</div>
