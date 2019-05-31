<?php
/* @var $users \common\models\Users[] */

/* @var $user_property */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Пользователи');
?>
<div class="orders-index box-padding-index">

    <h2 class="page-header">Пользователи</h2>
    <?php
    $count = 0;
    foreach ($users as $user) {
        if ($count % 4 == 0) {
            if ($count > 0) print '</div>';
            print '<div class="row">';
        }
        $path = $user->getPhotoUrl();
        if (!$path || !$user['image']) {
            $path = '/images/unknown.png';
        }

        print '<div class="col-md-3">
                        <div class="box box-widget widget-user-2">
                        <div class="widget-user-header bg-yellow">
                        <div class="widget-user-image">';
        echo Html::a('<img class="img-circle" src="' . Html::encode($path) . '" style="width:65px; float: left">',
            ['/users/view', 'id' => Html::encode($user['_id'])]);
        print '</div>';
        print '<h3 class="widget-user-username" style="color: white; font-size: 24px">' . $user['name'] . '</h3>';
        print '<h5 class="widget-user-desc" style="color: white; font-size: 14px">' . $user['contact'] . '</h5>';
        print '</div>
                        <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">Фотографий
                            <span class="pull-right badge bg-blue">' . $user_property[$count]['photos'] . '</span></a></li>
                            <li><a href="#">Сообщений 
                            <span class="pull-right badge bg-green">' . $user_property[$count]['messages'] . '</span></a></li>
                            <li><a href="#">Домов/квартир 
                            <span class="pull-right badge bg-orange">' . count($user_property[$count]['houses']) . ' / ' .
            $user_property[$count]['objects'] . '</span></a>
                            </li>
                            <li>'.Html::a('Посещено <span class="pull-right badge bg-green">' . $user_property[$count]['visited'] . ' / ' .
                            $user_property[$count]['visited_total'] . '%</span>',
                            ['/users/table', 'id' => Html::encode($user['_id']),
                                'date_start' => '2018-09-27 00:00:00',
                                'date_end' => '2018-10-07 00:00:00']).' 
                            </li>
                            <li><a href="#">Треков передвижения 
                            <span class="pull-right badge bg-yellow">' . $user_property[$count]['tracks'] . '</span></a></li>
                            <li>'.Html::a('Обход №1 2018/09/27 - 2018/10/03',
                ['/users/table', 'id' => Html::encode($user['_id']),
                    'date_start' => '2018-09-27 00:00:00',
                    'date_end' => '2018-10-03 23:50:00']).' 
                            </li>
                            <li>'.Html::a('Обход №2 2018/10/04 - 2018/10/18',
                ['/users/table', 'id' => Html::encode($user['_id']),
                    'date_start' => '2018-10-04 00:00:00',
                    'date_end' => '2018-10-18 00:00:00']).' 
                            </li>
                            
                        </ul>
                    </div>
                    </div>
                </div>';
        $count++;
    }
    ?>
</div>
