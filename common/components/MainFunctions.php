<?php

namespace common\components;

use common\models\DeviceStatus;
use common\models\Journal;
use common\models\TaskVerdict;
use common\models\User;
use common\models\Users;
use common\models\WorkStatus;
use Yii;

class MainFunctions
{
    /**
     * Возвращает разницу во времени для операций. Проверяет время на корректность.
     *
     * @param string $beginDate - дата начала
     * @param string $endDate - дата окончания
     * @param integer $limit - предел в секундах
     *
     * @return integer Время выполнения.
     */
    public static function getOperationLength($beginDate, $endDate, $limit)
    {
        // даты должны быть не из прошлого века
        // дата окончания старше даты начала
        // время выполнения не больше лимита
        if (strtotime($endDate) > 10000000 && strtotime($beginDate) > 10000000 && strtotime($endDate) > strtotime($beginDate) && (strtotime($endDate) - strtotime($beginDate)) < $limit) {
            return strtotime($endDate) - strtotime($beginDate);
        } else
            return 0;
    }

    static function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Возвращает  случайный цвет в hex формате.
     *
     * @return string Цвет в hex формате.
     */
    public static function random_color()
    {
        return MainFunctions::random_color_part() . MainFunctions::random_color_part() . MainFunctions::random_color_part();
    }

    /**
     * Logs one or several messages into daemon log file.
     * @param string $filename
     * @param array|string $messages
     */
    public static function log($filename, $messages)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            file_put_contents($filename, date('d.m.Y H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Logs message to journal register in db
     * @param string $description сообщение в журнал
     * @return integer код ошибкиы
     */
    public static function register($description)
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = User::find()
            ->where(['_id' => $accountUser['id']])
            ->asArray()
            ->one();
        $journal = new Journal();
        $journal->userUuid = $currentUser['uuid'];
        $journal->description = $description;
        $journal->date = date('Y-m-d H:i:s');
        if ($journal->save())
            return Errors::OK;
        else {
            return Errors::ERROR_SAVE;
        }
    }

    /**
     * return generated UUID
     * @return string generated UUID
     */
    static function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    static function logs($str)
    {
        $handle = fopen("1.txt", "r+");
        fwrite($handle, $str);
        fclose($handle);
    }

    /**
     * Find nearest location of user by coordinates
     * @param Users $user
     * @param boolean $full
     * @return string название локации
     */
    public static function getLocationByUser($user, $full)
    {
        $location = 'не определено';
        /*        $gps = Gpstrack::find()->where(['userUuid' => $user['uuid']])->orderBy('date DESC')->one();
                $objects = Objects::find()->all();
                $max_distance=10;
                foreach ($objects as $object) {
                    $distance=abs(sqrt(($object['latitude']-$gps['latitude'])^2+($object['longitude']-$gps['longitude'])^2));
                    if ($distance<$max_distance) {
                        $max_distance = $distance;
                        $location = $object['title'];
                        if ($full)
                            $location .= ' ['.$gps['latitude'].', '.$gps['longitude'].']';
                    }
                }*/
        return $location;
    }

    /**
     * Sort array by param
     * @param $array
     * @param $cols
     * @return array
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    public static function getColorLabelByStatus($status, $type)
    {
        $label = '<div class="progress"><div class="critical3">' . $status['title'] . '</div></div>';
        if ($type == 'task_status') {
            if ($status["uuid"] == WorkStatus::NEW_OPERATION ||
                $status["uuid"] == WorkStatus::IN_WORK)
                $label = '<div class="progress"><div class="critical5">' . $status['title'] . '</div></div>';
            if ($status["uuid"] == WorkStatus::CANCELED)
                $label = '<div class="progress"><div class="critical2">' . $status['title'] . '</div></div>';
            if ($status["uuid"] == WorkStatus::UN_COMPLETE)
                $label = '<div class="progress"><div class="critical1">' . $status['title'] . '</div></div>';
        }
        if ($type == 'task_verdict') {
            if ($status["uuid"] == TaskVerdict::NOT_DEFINED)
                $label = '<div class="progress"><div class="critical5">' . $status['title'] . '</div></div>';
            if ($status["uuid"] == TaskVerdict::INSPECTED)
                $label = '<div class="progress"><div class="critical1">' . $status['title'] . '</div></div>';
        }
        if ($type == 'equipment_status') {
            if ($status['uuid'] == DeviceStatus::NOT_MOUNTED) {
                $label = 'critical1';
            } elseif ($status['uuid'] == DeviceStatus::NOT_WORK) {
                $label = 'critical2';
            } elseif ($status['uuid'] == DeviceStatus::UNKNOWN) {
                $label = 'critical4';
            } else {
                $label = 'critical3';
            }
        }
        return $label;
    }

    public static function getAddButton($link)
    {
        return "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\">
        <a href=\"" . $link . "\">
        <button class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
        </button></a></span></div>\n{hint}\n{error}";
    }

}

