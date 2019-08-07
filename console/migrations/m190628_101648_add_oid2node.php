<?php

use yii\db\Migration;
use common\models\Node;

/**
 * Class m190628_101648_add_oid2node
 */
class m190628_101648_add_oid2node extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!isset(Yii::$app->params['node']['oid'])) {
            return false;
        }

        $this->addColumn('{{%node}}', 'oid', $this->integer(11)->notNull()->defaultValue(0));
        $this->createIndex('idx-node-oid', '{{%node}}', ['_id', 'oid']);
        $nodes = Node::find()->all();
        foreach ($nodes as $node) {
            $node->oid = Yii::$app->params['node']['oid'];
            $node->save();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190628_101648_add_oid2node cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190628_101648_add_oid2node cannot be reverted.\n";

        return false;
    }
    */
}
