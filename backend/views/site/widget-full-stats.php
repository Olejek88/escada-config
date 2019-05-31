<?php

/* @var $cityCount
 * @var $streetCount
 * @var $objectCount
 * @var $nodesCount
 * @var $channelsCount
 * @var $deviceCount
 * @var $deviceTypeCount
 * @var $contragentCount
 * @var $usersCount
*/
?>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/city"><span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span></a>

        <div class="info-box-content">
            <span>Городов <?= $cityCount; ?> / Улиц <?= $streetCount; ?></span><br/>
            <span>Объектов <?= $objectCount; ?> /  Шкафов <?= $nodesCount; ?></span><br/>
            <span>Каналов <?= $channelsCount; ?></span><br/>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/device"><span class="info-box-icon bg-red"><i class="fa fa-plug"></i></span></a>

        <div class="info-box-content">
            <a href="/device"><span class="info-box-text">Оборудование</span></a>
            <span><a href="/device-type">Типов <?= $deviceTypeCount; ?></a></span><br/>
            <span class="info-box-number"><?= $deviceCount ?></span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->

<!-- fix for small devices only -->
<div class="clearfix visible-sm-block"></div>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/objects"><span class="info-box-icon bg-green"><i class="fa fa-map-marker"></i></span></a>

        <div class="info-box-content">
            <span class="info-box-text">Субъекты</span>
            <span>Организации <?= $contragentCount; ?></span><br/>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/users/dashboard"><span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span></a>
        <div class="info-box-content">
            <span class="info-box-text">Пользователи</span>
            <span>Всего / Активных</span>
            <span class="info-box-number"><?= $usersCount ?>/<?= $usersCount ?></span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
