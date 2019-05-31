<?php

use common\models\User;
use yii\helpers\Html;

/* @var $model \common\models\Users */
/* @var $user_property */
/* @var $orders */
/* @var $events */
/* @var $tree */

$this->title = 'Профиль пользователя :: ' . $model->name;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Профиль пользователя
        </h1>
        <ol class="breadcrumb">
            <li><?php echo Html::a('Главная', '/') ?></li>
            <li><?php echo Html::a('Пользователи', '/users/dashboard') ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?php
                        $path = $model->getPhotoUrl();
                        if (!$path || !$model['image']) {
                            $path = '/images/unknown2.png';
                        }
                        echo '<img class="profile-user-img img-responsive img-circle" src="' . Html::encode($path) . '">';
                        ?>
                        <h3 class="profile-username text-center"><?php echo $model['name'] ?></h3>
                        <p class="text-muted text-center"><?php echo $model['whoIs'] ?></p>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Фотографий</b> <a class="pull-right"><?php echo $user_property['photo'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Измерений</b> <a class="pull-right"><?php echo $user_property['measure'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Предупреждений</b> <a class="pull-right"><?php echo $user_property['alarms'] ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Собщений</b> <a class="pull-right"><?php echo $user_property['messages'] ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Информация</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <strong><i class="fa fa-mobile margin-r-5"></i> Контакт</strong>
                        <span class="text-muted">
                            <?php echo $model['contact'] ?>
                        </span>
                        <hr>
                        <strong><i class="fa fa-tag margin-r-5"></i> Код</strong>
                        <span class="text-muted">
                            <?php echo $model['pin'] ?>
                        </span>
                        <hr>
                        <strong><i class="fa fa-map-marker margin-r-5"></i> Координаты</strong>
                        <p class="text-muted">
                            <?php echo $user_property['location'] ?>
                        </p>
                        <hr>

                        <strong><i class="fa fa-check-circle margin-r-5"></i> Статус</strong>
                        <?php
                        echo '<span class="label label-success">Активен</span>';
                        ?>

                        <hr>
                        <strong><i class="fa fa-pencil margin-r-5"></i> Специализация</strong>
                        <p>
                            <?php
                            if (\Yii::$app->user->can(User::PERMISSION_ADMIN)) {
                                echo '<span class="label label-danger">Администратор</span>';
                            }
                            ?>
                            <?php
                            if (\Yii::$app->user->can(User::PERMISSION_OPERATOR)) {
                                echo '<span class="label label-success">Оператор</span>';
                            }
                            ?>
                            <?php
                            echo '<span class="label label-info">Персонал</span>';
                            ?>
<!--                            --><?php
/*                            if (\Yii::$app->user->can(User::PERMISSION_CONTRACTOR)) {
                                echo '<span class="label label-warning">Заказчик</span>';
                            }
                            */?>
                        </p>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-right: 0"><a href="#timeline" data-toggle="tab">Журнал</a>
                        </li>
                        <li style="margin-right: 0"><a href="#activity" data-toggle="tab">Активность</a></li>
                        <li style="margin-right: 0"><a href="#settings" data-toggle="tab">Настройки</a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="active tab-pane" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">
                                <?php
                                foreach ($events as $event) {
                                    echo $event['event'];
                                }
                                ?>
                            </ul>
                        </div>

                        <div class="tab-pane" id="activity">
                            <!-- Post -->
                            <?php
                            /*                                $orderCount=0;
                                                            foreach ($orders as $order) {
                                                                echo '<div class="post"><div class="user-block">';
                                                                $path = $order['author']->getImageUrl();
                                                                if (!$path || !$order['author']['image']) {
                                                                    $path='/images/unknown.png';
                                                                }
                                                                echo '<img class="img-circle img-bordered-sm" src="'.Html::encode($path).'">';
                                                                if ($order['startDate']>0) $startDate = date("M j, Y", strtotime($order['startDate']));
                                                                else $startDate = 'не назначен';
                                                                if ($order['openDate']>0) $openDate = date("M j, Y", strtotime($order['openDate']));
                                                                else $openDate = 'не начинался';
                                                                if ($order['closeDate']>0) $closeDate = date("M j, Y", strtotime($order['openDate']));
                                                                else $closeDate = 'не закончился';

                                                                echo '<span class="username">
                                                                      <a href="#">'.$order['title'].'</a>
                                                                      <a href="#" class="pull-right btn-box-tool"><i class="fa fa-time"></i></a>
                                                                      </span>
                                                                      <span class="description">Назначен на '.$startDate.' ['.$openDate.' - '.$closeDate.']</span>
                                                                      </div>';
                                                                echo '<p>'.$tree[$orderCount].'</p>';
                                                                echo  '<ul class="list-inline">
                                                                        <li><a href="/orders/view?id='.$order["_id"].'" class="link-black text-sm"><i class="fa fa-share margin-r-5"></i> Редактировать</a></li>
                                                                        <li class="pull-right"><a href="#" class="link-black text-sm"><i class="fa fa-comments-o margin-r-5"></i> Сообщение по наряду</a></li>
                                                                        </ul>';
                                                                echo '</div>';
                                                                $orderCount++;
                                                            }*/
                            ?>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="settings">
                            <div class="post">
                                <div class="user-block">
                                    <?= $this->render('_form', [
                                        'model' => $model, ['class' => 'form-horizontal']
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->