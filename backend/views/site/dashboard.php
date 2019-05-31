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
 * @var $categories
 * @var $equipments Device[]
 * @var $usersCount
 * @var $currentUser
 * @var $objectsCount
 * @var $objectsTypeCount
 * @var $events
 * @var $users User[]
 * @var $objectsList
 * @var $objectsGroup
 * @var $usersList
 * @var $last_measures
 * @var $complete
 * @var $devicesGroup
 * @var $devicesList
 */

use common\models\Device;
use common\models\User;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Сводная');
?>

<br/>
<!-- Info boxes -->
<div class="row">
    <?= $this->render('widget-full-stats', ['cityCount' => $cityCount, 'streetCount' =>$streetCount, 'nodesCount' =>$nodesCount,
        'channelsCount' =>$channelsCount, 'deviceCount' =>$deviceCount, 'deviceTypeCount' =>$deviceTypeCount,
        'contragentCount' =>$contragentCount, 'usersCount' =>$usersCount, 'objectCount' =>$objectCount]); ?>
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
    </div>

    <div class="col-md-8">
        <?= $this->render('widget-map', ['coordinates' => $coordinates, 'devicesGroup' =>$devicesGroup, 'devicesList' =>$devicesList]); ?>
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
