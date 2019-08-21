<?php

namespace common\models\mtm;

/**
 *
 * @property mixed $base64Data
 * @property array $binaryData
 */
class MtmDevLightConfigLight extends MtmPktHeader
{
    const MAX_LIGHT_CONFIG = 5;
    public $device;
//    public $config;
    public $time; // время в минутах с начала суток
    public $value; // уровень освещения 0-100%

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->type = self::$MTM_CMD_TYPE_CONFIG_LIGHT;
        $this->device = MtmPktHeader::$MTM_DEVICE_LIGHT;
        for ($i = 0; $i < self::MAX_LIGHT_CONFIG; $i++) {
            //$this->config[$i] = ['time' => $i, 'value' => $i];
            $this->time[$i] = $i;
            $this->value[$i] = $i;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['device'], 'integer', 'min' => 0x01, 'max' => 0x01],
//            [['config'], 'each', 'rule' => ['checkConfigElement']],
            [['time', 'value'], 'required'],
            [['time'], 'integer', 'min' => 0, 'max' => 1440],
            [['value'], 'integer', 'min' => 0, 'max' => 100],
        ]);
    }

    public function checkConfigElement($attr, $param)
    {
        $configValues = $this->attributes[$attr];

        if (!is_array($configValues)) {
            $this->addError($attr, 'Должен быть массив с элементами time, value');
            return;
        }

        if (!array_key_exists('time', $configValues)) {
            $this->addError($attr, 'Должен быть элемент time');
            return;
        }

        if (!array_key_exists('value', $configValues)) {
            $this->addError($attr, 'Должен быть элемент value');
            return;
        }

        if (!is_numeric($configValues['time'])) {
            $this->addError($attr, 'time должен быть целым числом');
            return;
        }

        if (!is_numeric($configValues['value'])) {
            $this->addError($attr, 'value должен быть целым числом');
            return;
        }

        if ($configValues['time'] < 0 || $configValues['time'] > 0xffff) {
            $this->addError($attr, 'time должен быть в диапазоне 0x0000 - 0xffff');
            return;
        }

        if ($configValues['value'] < 0 || $configValues['value'] > 0xffff) {
            $this->addError($attr, 'value должен быть в диапазоне 0x0000 - 0xffff');
            return;
        }
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
        for ($i = 0; $i < self::MAX_LIGHT_CONFIG; $i++) {
            $binary[$i * 4 + 3] = $this->time[$i] & 0x00ff;
            $binary[$i * 4 + 4] = $this->time[$i] >> 8;
            $binary[$i * 4 + 5] = $this->value[$i] & 0x00ff;
            $binary[$i * 4 + 6] = $this->value[$i] >> 8;
        }

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
        for ($i = 0; $i < self::MAX_LIGHT_CONFIG; $i++) {
            $this->time[$i] = ord($data[$i * 4 + 3]) | (ord($data[$i * 4 + 4]) << 8);
            $this->value[$i] = ord($data[$i * 4 + 5]) | (ord($data[$i * 4 + 6]) << 8);
        }
        return $this->validate();
    }

}