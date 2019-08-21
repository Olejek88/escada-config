<?php

namespace common\models\mtm;

/**
 *
 * @property mixed $base64Data
 * @property array $binaryData
 */
class MtmDevLightActionSetLight extends MtmDevLightAction
{
    public static $MTM_DEV_LIGHT_ACTION_SET_LIGHT = 0x02;

    // команда установки уровня освещенности
    public $value;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->type = self::$MTM_CMD_TYPE_ACTION;
        $this->device = MtmPktHeader::$MTM_DEVICE_LIGHT;
        $this->action = self::$MTM_DEV_LIGHT_ACTION_SET_LIGHT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['action'], 'in', 'range' => [self::$MTM_DEV_LIGHT_ACTION_SET_LIGHT]],
            [['value'], 'integer', 'min' => 0, 'max' => 100],
        ]);
    }

    public function getBase64Data()
    {
        $string = '';
        foreach ($this->getBinaryData() as $value) {
            $string .= chr($value);
        }

        return base64_encode($string);
    }

    public function getBinaryData()
    {
        $binary = [];
        $binary[0] = $this->type;
        $binary[1] = $this->protoVersion;
        $binary[2] = $this->device;
        $binary[3] = $this->action;
        $binary[4] = $this->value;
        return $binary;
    }

    public function loadBase64Data($data)
    {
        $this->loadBinaryData(base64_decode($data));
    }

    public function loadBinaryData($data)
    {
        $this->type = ord($data[0]);
        $this->protoVersion = ord($data[1]);
        $this->device = ord($data[2]);
        $this->action = ord($data[3]);
        $this->value = ord($data[4]);
        return $this->validate();
    }
}