<?php

namespace common\models;

use common\components\MtmActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "node_control".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $nodeUuid
 * @property string $date
 * @property int $type
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Node $node
 */
class NodeControl extends MtmActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'node_control';
    }

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => function () {
                    return $this->scenario == self::SCENARIO_CUSTOM_UPDATE ? $this->changedAt : new Expression('NOW()');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'nodeUuid', 'type'], 'required'],
            [['date', 'createdAt', 'changedAt'], 'safe'],
            [['changedAt'], 'string', 'on' => self::SCENARIO_CUSTOM_UPDATE],
            [['type'], 'integer'],
            [['uuid', 'nodeUuid'], 'string', 'max' => 45],
            [['uuid'], 'unique'],
            [['nodeUuid', 'date', 'type'], 'unique', 'targetAttribute' => ['nodeUuid', 'date', 'type']],
            [
                ['nodeUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Node::class,
                'targetAttribute' => ['nodeUuid' => 'uuid']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'uuid' => 'Uuid',
            'nodeUuid' => 'Node Uuid',
            'date' => 'Date',
            'type' => 'Type',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['uuid' => 'nodeUuid']);
    }
}
