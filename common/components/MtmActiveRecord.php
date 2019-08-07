<?php

namespace common\components;

use yii\db\ActiveRecord;

class MtmActiveRecord extends ActiveRecord
{
    const SCENARIO_CUSTOM_UPDATE = 'custom_update';
}