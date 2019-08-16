<?php
/* @var $device
 * @var $parameters
 */

use common\models\Device;
use common\models\User;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Сводная страница по устройству');
?>

<br/>
<!-- Info boxes -->
<div class="row">
    <div class="col-md-12">
    <?= $this->render('widget-light-info', ['device' => $device]); ?>
    </div>
</div>
<!-- /.row -->

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
<div class="row">
    <div class="col-md-7">
        <?= $this->render('widget-light-register', ['device' => $device]); ?>
    </div>

    <div class="col-md-5">
        <?= $this->render('widget-light-list', ['device' => $device]); ?>
    </div>
</div>

