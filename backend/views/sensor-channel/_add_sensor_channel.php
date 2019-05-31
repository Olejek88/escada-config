<?php
/* @var $model common\models\SensorChannel */

use common\components\MainFunctions;
use common\models\SensorChannelStatus;
use common\models\Users;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'options'                => [
            'id'      => 'form'
        ]]);
?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Добавить канал измерения</h4>
    </div>
    <div class="modal-body">
        <?php
            echo $form->field($model, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);
            if (isset($_GET["deviceUuid"]))
                echo $form->field($model, 'deviceUuid')->hiddenInput(['value' => $_GET["deviceUuid"]])->label(false);
            echo $form->field($model, 'register')->textArea();
        ?>
    </div>
    <div class="modal-footer">
        <?php echo Html::submitButton(Yii::t('backend', 'Отправить'), ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
<script>
    $(document).on("beforeSubmit", "#form", function () {
        $.ajax({
            url: "../sensor-channel/new",
            type: "post",
            data: $('form').serialize(),
            success: function () {
                $('#modal_request').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });
</script>
<?php ActiveForm::end(); ?>
