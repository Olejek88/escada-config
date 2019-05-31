<?php

use common\models\Journal;
use common\models\OrderStatus;

$journals = Journal::find()->select('*')->orderBy('date DESC')->limit(3)->all();
?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Последняя активность</h3>
            <ul class="control-sidebar-menu">
                <?php
                $count = 0;
                foreach ($journals as $journal) {
                    print '<li><a href="javascript:void(0)">';
                    if (strstr($journal['description'], 'наряд'))
                        print '<i class="menu-icon fa fa-file-code-o bg-green"></i>';
                    if (strstr($journal['description'], 'пользоват'))
                        print '<i class="menu-icon fa fa-user bg-yellow"></i>';
                    print '<div class="menu-info">
                                <h4 class="control-sidebar-subheading">' . $journal['date'] . '</h4>
                           <p>' . $journal['description'] . '</p>
                           </div></a></li>';
                }
                ?>
            </ul>
            <!-- /.control-sidebar-menu -->

            <h3 class="control-sidebar-heading">Прогресс нарядов</h3>
            <ul class="control-sidebar-menu">
                <?php
                $orders = $this->params['lastOrders'];
                foreach ($orders as $order) {
                    $percent = 0;
                    $color = 'label-danger';
                    if ($order['orderStatusUuid'] == OrderStatus::COMPLETE) {
                        $percent = 100;
                        $color = 'label-info';
                    }
                    if ($order['orderStatusUuid'] == OrderStatus::IN_WORK) {
                        $percent = 55;
                        $color = 'label-warning';
                    }
                    echo '<li>
                                <a href="/orders/view?id=' . $order["_id"] . '">
                                <h4 class="control-sidebar-subheading">' . $order['title'] . '
                                <span class="label ' . $color . ' pull-right">' . $percent . '%</span>
                                </h4>
                                <div class="progress progress-xxs">
                                    <div class="progress-bar progress-bar-danger" style="width: ' . $percent . '%"></div>
                                </div>
                                </a>
                            </li>';
                }
                ?>
            </ul>
            <!-- /.control-sidebar-menu -->

        </div>
        <!-- /.tab-pane -->
        <!-- Stats tab content -->
        <div class="tab-pane" id="control-sidebar-stats-tab">Настройки</div>
        <!-- /.tab-pane -->
        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
                <h3 class="control-sidebar-heading">Основные настройки</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Настройка
                        <input type="checkbox" class="pull-right" checked>
                    </label>
                    <p>
                        Полный вывод информации
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Показывать уведомления
                        <input type="checkbox" class="pull-right" checked>
                    </label>
                    <p>
                        Разрешает push уведомления
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Разрешает генерацию нарядов
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Разрешить/запретить автоматическое добавление
                    </p>
                </div>
                <!-- /.form-group -->

                <h3 class="control-sidebar-heading">Функционал</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Показывать мой статус
                        <input type="checkbox" class="pull-right" checked>
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Отключить отправку сообщений
                        <input type="checkbox" class="pull-right">
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Удалить журнал при выходе
                        <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                    </label>
                </div>
                <!-- /.form-group -->
            </form>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
