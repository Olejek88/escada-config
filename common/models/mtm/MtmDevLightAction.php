<?php

namespace common\models\mtm;

/**
 *
 * @property mixed $base64Data
 * @property array $binaryData
 */
class MtmDevLightAction extends MtmPktHeader
{
    public static $MTM_DEV_LIGHT_ACTION_SET_LIGHT = 0x02;
    public $device;

    // команда установки уровня освещенности
    public $action;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->type = self::$MTM_CMD_TYPE_ACTION;
        $this->device = MtmPktHeader::$MTM_DEVICE_LIGHT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['device'], 'integer', 'min' => 0x01, 'max' => 0x01],
        ]);
    }
}