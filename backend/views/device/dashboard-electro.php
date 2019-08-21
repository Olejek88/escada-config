<?php
/* @var $device
 * @var $parameters
 * @var $dataAll
 */

$this->title = Yii::t('app', 'Устройство');
?>

<br/>
<!-- Info boxes -->
<!-- /.row -->

<div class="row">
    <!-- Left col -->
    <div class="col-md-7">
        <?= $this->render('widget-electro-integrate', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>
    <div class="col-md-5">
        <?= $this->render('widget-electro-register', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $this->render('widget-electro-trends', ['device' => $device, 'parameters' => $parameters]); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-electro-power', ['device' => $device, 'parameters' => $parameters]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-electro-params', ['device' => $device, 'parameters' => $parameters]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <?= $this->render('widget-electro-archive-days', ['device' => $device, 'parameters' => $parameters, 'dataAll' => $dataAll]); ?>
    </div>
</div>

