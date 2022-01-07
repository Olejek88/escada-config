<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'on afterOpen' => function($event) {
                $date = new DateTime();
                $offset = $date->getOffset();
                $sign = $offset < 0 ? '-' : '+';
                $offset = abs($offset);
                $hour = intval($offset / (60 * 60));
                $min = abs(abs($offset) - abs($hour) * (60 * 60)) / 60;
                $tzFinal = $sign . $hour . ':' . $min;
                $event->sender->createCommand("SET time_zone='" . $tzFinal . "';")->execute();
            },
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
//        '@bower-asset' => '@vendor/bower',
        '@npm'   => '@vendor/npm-asset',
//        '@npm-asset'   => '@vendor/npm',
    ],
    'timeZone' => 'Asia/Yekaterinburg',
];
