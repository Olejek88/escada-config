<?php

namespace common\models\mtm;

use yii\base\Model;

class MtmPktHeader extends Model
{
    public static $MTM_CMD_TYPE_CONFIG = 2;
    public static $MTM_CMD_TYPE_CONFIG_LIGHT = 3;
    public static $MTM_CMD_TYPE_ACTION = 5;
    public static $MTM_CMD_TYPE_CONTACTOR = 6;
    public static $MTM_CMD_TYPE_RESET_COORDINATOR = 7;

    public static $MTM_CMD_PROTO_VERSION_0 = 0;

    // индекс устройства(датчика) типа "светильник"
    public static $MTM_DEVICE_LIGHT = 0;

    public $type;
    public $protoVersion;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->protoVersion = self::$MTM_CMD_PROTO_VERSION_0;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'protoVersion'], 'required'],
            [['type', 'protoVersion'], 'integer'],
            ['type', 'in', 'range' => [self::$MTM_CMD_TYPE_CONFIG, self::$MTM_CMD_TYPE_CONFIG_LIGHT, self::$MTM_CMD_TYPE_ACTION, self::$MTM_CMD_TYPE_CONTACTOR, self::$MTM_CMD_TYPE_RESET_COORDINATOR]],
            ['protoVersion', 'in', 'range' => [self::$MTM_CMD_PROTO_VERSION_0]],
        ];
    }

}