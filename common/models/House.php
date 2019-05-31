<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "house".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $number
 * @property string $streetUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property string $houseTypeUuid
 * @property boolean $deleted
 *
 * @property Street $street
 * @property HouseType $houseType
 */
class House extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'streetUuid'], 'required'],
            [['createdAt', 'changedAt', 'deleted'], 'safe'],
            [['uuid', 'number', 'houseTypeUuid', 'streetUuid', 'oid'], 'string', 'max' => 50],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'number',
            'houseTypeUuid',
            'houseType' => function ($model) {
                return $model->houseType;
            },
            'streetUuid',
            'street' => function ($model) {
                return $model->street;
            },
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'number' => Yii::t('app', 'Номер дома'),
            'street' => Yii::t('app', 'Улица'),
            'streetUuid' => Yii::t('app', 'Улица'),
            'houseTypeUuid' => Yii::t('app', 'Тип дома'),
            'houseType' => Yii::t('app', 'Тип дома'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasMany(Photo::class, ['houseUuid' => 'uuid']);
    }

    public function getStreet()
    {
        return $this->hasOne(Street::class, ['uuid' => 'streetUuid']);
    }

    public function getHouseType()
    {
        return $this->hasOne(HouseType::class, ['uuid' => 'houseTypeUuid']);
    }

    public function getFullTitle() {
//        echo json_encode($this->houseType);
        return 'ул.'.$this->street['title'].', д.'.$this->number;
    }
}
