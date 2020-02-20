<?php

use yii\db\Migration;

/**
 * Class m200220_153757_create_indexes
 */
class m200220_153757_create_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('camera', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('camera', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'camera', ['createdAt']);
        $this->createIndex('changedAt-idx', 'camera', ['changedAt']);
        $this->createIndex('title-idx', 'camera', ['title']);
        $this->createIndex('address-idx', 'camera', ['address']);

        $this->db->createCommand("alter table data modify date timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'data', ['createdAt']);
        $this->createIndex('changedAt-idx', 'data', ['changedAt']);
        $this->createIndex('type-idx', 'data', ['type']);
        $this->createIndex('parameter-idx', 'data', ['parameter']);

        $this->alterColumn('device', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('device', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'device', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device', ['changedAt']);
        $this->createIndex('address-idx', 'device', ['address']);
        $this->createIndex('last_date-idx', 'device', ['last_date']);
        $this->createIndex('q_att-idx', 'device', ['q_att']);
        $this->createIndex('q_errors-idx', 'device', ['q_errors']);
        $this->createIndex('dev_time-idx', 'device', ['dev_time']);
        $this->createIndex('name-idx', 'device', ['name']);
        $this->createIndex('port-idx', 'device', ['port']);
        $this->createIndex('protocol-idx', 'device', ['protocol']);
        $this->createIndex('number-idx', 'device', ['number']);
        $this->createIndex('serial-idx', 'device', ['serial']);
        $this->createIndex('interface-idx', 'device', ['interface']);
        $this->createIndex('linkTimeout-idx', 'device', ['linkTimeout']);

        $this->alterColumn('device_config', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('device_config', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'device_config', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device_config', ['changedAt']);
        $this->createIndex('parameter-idx', 'device_config', ['parameter']);
        $this->createIndex('value-idx', 'device_config', ['value']);

        $this->alterColumn('device_program', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->alterColumn('device_program', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->createIndex('createdAt-idx', 'device_program', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device_program', ['changedAt']);

        $this->alterColumn('device_register', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('device_register', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'device_register', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device_register', ['changedAt']);
        $this->createIndex('date-idx', 'device_register', ['date']);

        $this->alterColumn('device_status', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('device_status', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'device_status', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device_status', ['changedAt']);

        $this->alterColumn('device_type', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('device_type', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'device_type', ['createdAt']);
        $this->createIndex('changedAt-idx', 'device_type', ['changedAt']);

        $this->alterColumn('group', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->alterColumn('group', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->createIndex('createdAt-idx', 'group', ['createdAt']);
        $this->createIndex('changedAt-idx', 'group', ['changedAt']);
        $this->createIndex('title-idx', 'group', ['title']);
        $this->createIndex('groupId-idx', 'group', ['groupId']);

        $this->execute("update group_control set date='1970-01-02 00:00:00' where date<'1971-01-01'");
        $this->db->createCommand("alter table group_control modify date timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'group_control', ['createdAt']);
        $this->createIndex('changedAt-idx', 'group_control', ['changedAt']);
        $this->createIndex('groupUuid-idx', 'group_control', ['groupUuid']);
        $this->createIndex('date-idx', 'group_control', ['date']);
        $this->createIndex('type-idx', 'group_control', ['type']);

        $this->db->createCommand("alter table info modify date timestamp default '1970-01-02 00:00:00', modify time timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'info', ['createdAt']);
        $this->createIndex('changedAt-idx', 'info', ['changedAt']);

        $this->alterColumn('interfaces', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('interfaces', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'interfaces', ['createdAt']);
        $this->createIndex('changedAt-idx', 'interfaces', ['changedAt']);

        $this->createIndex('date-idx', 'journal', ['date']);

        $this->db->createCommand("alter table last_update modify date timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'last_update', ['createdAt']);
        $this->createIndex('changedAt-idx', 'last_update', ['changedAt']);
        $this->createIndex('date-idx', 'last_update', ['date']);

        $this->db->createCommand("alter table light_answer modify dateIn timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'light_answer', ['createdAt']);
        $this->createIndex('changedAt-idx', 'light_answer', ['changedAt']);
        $this->createIndex('address-idx', 'light_answer', ['address']);
        $this->createIndex('dateIn-idx', 'light_answer', ['dateIn']);
        $this->createIndex('dateOut-idx', 'light_answer', ['dateOut']);

        $this->db->createCommand("alter table light_message modify dateIn timestamp default CURRENT_TIMESTAMP, modify createdAt timestamp default '1970-01-02 00:00:00', modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'light_message', ['createdAt']);
        $this->createIndex('changedAt-idx', 'light_message', ['changedAt']);
        $this->createIndex('address-idx', 'light_message', ['address']);
        $this->createIndex('dateIn-idx', 'light_message', ['dateIn']);
        $this->createIndex('dateOut-idx', 'light_message', ['dateOut']);

        $this->alterColumn('measure_type', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('measure_type', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'measure_type', ['createdAt']);
        $this->createIndex('changedAt-idx', 'measure_type', ['changedAt']);

        $this->alterColumn('message', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('message', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'message', ['createdAt']);
        $this->createIndex('changedAt-idx', 'message', ['changedAt']);

        $this->alterColumn('node', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('node', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'node', ['createdAt']);
        $this->createIndex('changedAt-idx', 'node', ['changedAt']);

        $this->db->createCommand("alter table node_control modify date timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'node_control', ['createdAt']);
        $this->createIndex('changedAt-idx', 'node_control', ['changedAt']);
        $this->createIndex('date-idx', 'node_control', ['date']);
        $this->createIndex('type-idx', 'node_control', ['type']);

        $this->alterColumn('protocols', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('protocols', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'protocols', ['createdAt']);
        $this->createIndex('changedAt-idx', 'protocols', ['changedAt']);

        $this->alterColumn('sensor_channel', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('sensor_channel', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'sensor_channel', ['createdAt']);
        $this->createIndex('changedAt-idx', 'sensor_channel', ['changedAt']);
        $this->createIndex('measureTypeUuid-idx', 'sensor_channel', ['measureTypeUuid']);

        $this->alterColumn('sensor_config', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('sensor_config', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'sensor_config', ['createdAt']);
        $this->createIndex('changedAt-idx', 'sensor_config', ['changedAt']);

        $this->alterColumn('sound_file', 'createdAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('sound_file', 'changedAt', $this->timestamp()->defaultValue('1970-01-02 00:00:00'));
        $this->createIndex('createdAt-idx', 'sound_file', ['createdAt']);
        $this->createIndex('changedAt-idx', 'sound_file', ['changedAt']);
        $this->createIndex('deleted-idx', 'sound_file', ['deleted']);

        $this->db->createCommand("alter table stat modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'stat', ['createdAt']);
        $this->createIndex('changedAt-idx', 'stat', ['changedAt']);
        $this->createIndex('type-idx', 'stat', ['type']);

        $this->db->createCommand("alter table threads modify c_time timestamp default '1970-01-02 00:00:00', modify createdAt timestamp default CURRENT_TIMESTAMP, modify changedAt timestamp default '1970-01-02 00:00:00'")->execute();
        $this->createIndex('createdAt-idx', 'threads', ['createdAt']);
        $this->createIndex('changedAt-idx', 'threads', ['changedAt']);
        $this->createIndex('port-idx', 'threads', ['port']);
        $this->createIndex('speed-idx', 'threads', ['speed']);
        $this->createIndex('status-idx', 'threads', ['status']);
        $this->createIndex('work-idx', 'threads', ['work']);
        $this->createIndex('c_time-idx', 'threads', ['c_time']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_153757_create_indexes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_153757_create_indexes cannot be reverted.\n";

        return false;
    }
    */
}
