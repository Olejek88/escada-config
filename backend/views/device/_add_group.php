<?php
/* @var $group Group */

use common\components\MainFunctions;
use common\models\Group;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/device/group-save',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Группа</h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($group, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);

    echo $form->field($group, 'title')->textInput(['maxlength' => true]);
    echo $form->field($group, 'oid')->hiddenInput(['value' => User::getOid(Yii::$app->user->identity)])->label(false);
    ?>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: new FormData( this ),
            processData: false,
            contentType: false
            url: "../device/group-save",
            success: function () {
                $('#modalAddGroup').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
