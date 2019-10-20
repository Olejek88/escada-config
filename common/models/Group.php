<?php

namespace common\models;

use common\components\MtmActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "group".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $title
 * @property int $groupId
 * @property string $deviceProgramUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property DeviceProgram $deviceProgram
 */
class Group extends MtmActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
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
            [['uuid', 'title', 'groupId'], 'required'],
            [['groupId'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['changedAt'], 'string', 'on' => self::SCENARIO_CUSTOM_UPDATE],
            [['uuid', 'deviceProgramUuid'], 'string', 'max' => 45],
            [['title'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
            [
                ['deviceProgramUuid'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DeviceProgram::class,
                'targetAttribute' => ['deviceProgramUuid' => 'uuid']
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
            'title' => 'Title',
            'groupId' => 'Group ID',
            'deviceProgramUuid' => 'Device Program Uuid',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceProgram()
    {
        return $this->hasOne(DeviceProgram::class, ['uuid' => 'deviceProgramUuid']);
    }
}
