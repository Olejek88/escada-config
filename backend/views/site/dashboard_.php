<?php
/* @var $cityCount
 * @var $streetCount
 * @var $objectCount
 * @var $nodesCount
 * @var $channelsCount
 * @var $deviceCount
 * @var $deviceTypeCount
 * @var $contragentCount
 * @var $measures
 * @var $devices
 * @var $coordinates
 * @var $sumStageStatusCompleteCount
 * @var $sumOperationStatusCount
 * @var $sumOperationStatusCompleteCount
 * @var $categories
 * @var $bar
 * @var $orders
 * @var $equipments Device[]
 * @var $messagesChat
 * @var $usersCount
 * @var $currentUser
 * @var $objectsCount
 * @var $objectsTypeCount
 * @var $events
 * @var $services
 * @var $users User[]
 * @var $equipmentTypesCount
 * @var $modelsCount
 * @var $documentationCount
 * @var $trackCount
 * @var $objectsList
 * @var $objectsGroup
 * @var $usersList
 * @var $last_measures
 * @var $complete
 * @var $equipmentsGroup
 */

use common\models\Device;
use common\models\User;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Сводная');
?>

<br/>
<!-- Info boxes -->
<div class="row">
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
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Статистика измерений</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-wrench"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/measure">Измерения</a></li>
                            <li class="divider"></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart">
                            <div id="container" style="height: 250px;"></div>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </div>


    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Карта объектов и пользователей</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-md-9 col-sm-8" style="width: 100%">
                        <div class="pad" style="padding: 1px">
                            <div id="mapid" style="width: 100%; height: 360px"></div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        <div class="row">
            <div class="col-md-12">
                <!-- USERS LIST -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Операторы</h3>

                        <div class="box-tools pull-right">
                            <span class="label label-info">Операторов: <?= count($users) ?></span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <ul class="users-list clearfix">
                            <?php
                            $count = 0;
                            foreach ($users as $user) {
                                $path = $user->getPhotoUrl();
                                if (!$path || !$user['image']) {
                                    $path = '/images/unknown.png';
                                }
                                print '<li style="width:23%"><img src="' . Html::encode($path) . '" alt="User Image" width="145px">';
                                echo Html::a(Html::encode($user['name']),
                                    ['/users/view', '_id' => Html::encode($user['_id'])], ['class' => 'users-list-name']);
                                echo '<span class="users-list-date">' . $user['created_at'] . '</span></li>';
                            }
                            ?>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <?php echo Html::a('Все операторы', ['/users/dashboard'],
                            ['class' => 'btn btn-sm btn-info btn-flat pull-left']); ?>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!--/.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Последние измерения</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Адрес</th>
                            <th>Оборудование</th>
                            <th>Данные</th>
                            <th>Исполнитель</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 0;
                        foreach ($measures as $measure) {
                            print '<tr><td><a href="/measure/view?id=' . $measure["_id"] . '">' . $measure["_id"] . '</a></td>
                                        <td>' . $measure["date"] . '</td>
                                        <td>' . $measure["sensorChannel"]["device"]["object"]->getFullTitle().'</td>
                                        <td>' . $measure["sensorChannel"]["device"]["deviceType"]->title . '</td>
                                        <td>' . $measure["value"] . '</td></tr>';
                            $count++;
                            if ($count > 7) break;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="/measure/table" class="btn btn-sm btn-default btn-flat pull-right">Посмотреть все измерения</a>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box -->
    </div>

    <!-- /.col -->

    <div class="col-md-4">
        <?= $this->render('widget-equipments', ['devices' => $devices]); ?>
    </div>
</div>
<!-- /.content-wrapper -->

<footer class="main-footer" style="margin-left: 0 !important;">
    <div class="pull-right hidden-xs" style="vertical-align: middle; text-align: center;">
        <b>Version</b> 0.0.3
    </div>
    <?php echo Html::a('<img src="images/mtm.png">', 'http://www.mtm-smart.com'); ?>
    <strong>Copyright &copy; 2019 <a href="http://www.mtm-smart.com">MTM Смарт</a>.</strong> Все права на
    программный продукт защищены.
</footer>

<script>
    var houseIcon = L.icon({
        iconUrl: '/images/house_marker2.png',
        iconSize: [35, 35],
        iconAnchor: [22, 94],
        popupAnchor: [-3, -76]
    });

    <?php
    echo $equipmentsGroup;
    ?>

    var overlayMapsA = {};
    var overlayMapsB = {
        "Устройства":  devices
    };
    var map = L.map('mapid', {zoomControl: false, layers: [devices]}).setView(<?= $coordinates ?>, 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        id: 'mapbox.streets'
    }).addTo(map);

    L.control.layers(overlayMapsA, overlayMapsB, {
        position: 'bottomleft'
    }).addTo(map);

    L.control.zoom({
        position: 'bottomleft'
    }).addTo(map);

</script>
