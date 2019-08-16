<?php
/* @var $device */

use common\models\DeviceStatus;

?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Информация по  светильнику [<?php echo $device['name'] ?>]</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <div class="col-12">
            <?php
            if ($device['deviceStatusUuid'] == DeviceStatus::NOT_MOUNTED) {
                $class = 'critical1';
            } elseif ($device['deviceStatusUuid'] == DeviceStatus::NOT_WORK) {
                $class = 'critical2';
            } elseif ($device['deviceStatusUuid'] == DeviceStatus::UNKNOWN) {
                $class = 'critical4';
            } else {
                $class = 'critical3';
            }
            echo "<span class='badge' style='color: green; height: 12px; margin-top: -3px'> </span>";
            ?>
            <small class="float-right">Адрес: <?php echo $device['object']->getAddress() ?></small>
        </div>
    </div>
</div>
