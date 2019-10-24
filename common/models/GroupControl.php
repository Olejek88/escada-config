<?php

namespace common\models;

use common\components\MtmActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "group_control".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $groupUuid
 * @property string $date
 * @property int $type
 * @property string $deviceProgramUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property DeviceProgram $deviceProgram
 */
class GroupControl extends MtmActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_control';
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
            [['uuid', 'groupUuid', 'type'], 'required'],
            [['date', 'createdAt', 'changedAt'], 'safe'],
            [['changedAt'], 'string', 'on' => self::SCENARIO_CUSTOM_UPDATE],
            [['type'], 'integer'],
            [['uuid', 'groupUuid', 'deviceProgramUuid'], 'string', 'max' => 45],
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
            'groupUuid' => 'Group Uuid',
            'date' => 'Date',
            'type' => 'Type',
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
