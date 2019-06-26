<?php

use common\components\MainFunctions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Node;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\SoundFile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sound-file-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'sFile')->widget(FileInput::class,
        [
            'options' => ['accept' => 'audio/*', 'allowEmpty' => true],
            'pluginOptions' => ['allowedFileExtensions' => ['mp3']],
        ]
    ); ?>

    <?php
    $nodes = Node::find()->all();

    $items = ArrayHelper::map($nodes, 'uuid', function ($model) {
        return '[' . $model['address'] . ']';
    });
    echo $form->field($model, 'nodeUuid')->widget(Select2::class,
        [
            'data' => $items,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите контроллер..'
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
