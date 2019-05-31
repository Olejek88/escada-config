<?php

namespace console\controllers;


class DbManager extends \yii\rbac\DbManager
{

    public function init()
    {
        parent::init();

        // ручная установка используемой базы данных нужна в связи с тем, что DbManager не использует
        // параметры переданные в консоли, что непозволяет накатывать миграции на базу отличную от
        // указанной в конфиге приложения
        if (get_class(\Yii::$app->controller) === 'yii\console\controllers\MigrateController') {
            $db = \Yii::$app->controller->db;
            if (is_object($db)) {
                $this->db = $db;
            } elseif (isset(\Yii::$app->$db)) {
                $this->db = \Yii::$app->$db;
            }
        }
    }
}