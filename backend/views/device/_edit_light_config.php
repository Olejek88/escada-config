<?php
/* @var $device
 * @var $parameters
 */

use common\models\Device;
use common\models\User;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Конфигурация устройства');
?>

<br/>
<script>$.fn.slider = null</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
<div class="row">
    <!-- Left col -->
    <div class="col-md-3">
        <?= $this->render('widget-light-set', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>

    <div class="col-md-4">
        <?= $this->render('widget-light-params', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>

    <div class="col-md-5">
        <?= $this->render('widget-light-config', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>
</div>
</div>
