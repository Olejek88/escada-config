<?php

namespace console\yii2;

/**
 * Class Migration
 * @package console\yii2
 */
class Migration extends \yii\db\Migration
{
    // @link: http://denis.in.ua/foreign-keys-in-mysql.htm
    const FK_CASCADE = 'CASCADE';
    const FK_SET_NULL = 'SET NULL';
    const FK_NO_ACTION = 'NO ACTION';
    const FK_RESTRICT = 'RESTRICT';

    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        if ($options === null) {
            if ($this->db->driverName === 'mysql') {
                $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }
        }

        parent::createTable($table, $columns, $options);
    }
}
