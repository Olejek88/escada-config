<?php

namespace common\models\mtm;

/**
 *
 * @property mixed $base64Data
 * @property array $binaryData
 */
class MtmDevLightConfig extends MtmPktHeader
{
    // автоматический режим по заданным параметрам
    public static $MTM_DEV_LIGHT_CONFIG_MODE_AUTO = 0x01;
    // режим по командам от промкомпа астрономических событий
    public static $MTM_DEV_LIGHT_CONFIG_MODE_ASTRO = 0x02;
    // режим по командам от промкомпа со значением датчика освещенности
    public static $MTM_DEV_LIGHT_CONFIG_MODE_LIGHT_SENSOR = 0x03;

    // перечень доступных мощностей
    public static $LIGHT_POWER_12 = 0x00;
    public static $LIGHT_POWER_40 = 0x01;
    public static $LIGHT_POWER_60 = 0x02;
    public static $LIGHT_POWER_80 = 0x03;
    public static $LIGHT_POWER_100 = 0x04;
    public static $LIGHT_POWER_120 = 0x05;

    public $device; // должен быть 1
    public $mode; // Режим работы
    public $power; // Мощность установленного элемента
    public $group; // группа ?
    public $frequency; // Частота с которой отправляется статус светильника (в секундах)

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->type = self::$MTM_CMD_TYPE_CONFIG;
        $this->device = MtmPktHeader::$MTM_DEVICE_LIGHT;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['device', 'mode', 'power', 'frequency', 'group'], 'required'],
            [['device'], 'integer', 'min' => 0x01, 'max' => 0x01],
            [['mode'], 'in', 'range' => [
                self::$MTM_DEV_LIGHT_CONFIG_MODE_AUTO,
                self::$MTM_DEV_LIGHT_CONFIG_MODE_ASTRO,
                self::$MTM_DEV_LIGHT_CONFIG_MODE_LIGHT_SENSOR,
            ]],
            [['power'], 'in', 'range' => [
                self::$LIGHT_POWER_12,
                self::$LIGHT_POWER_40,
                self::$LIGHT_POWER_60,
                self::$LIGHT_POWER_80,
                self::$LIGHT_POWER_100,
                self::$LIGHT_POWER_120,
            ]],
            [['group'], 'integer', 'min' => 0, 'max' => 15],
            [['frequency'], 'integer', 'min' => 0, 'max' => 0xffff],
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
        $power = ord($this->power) << 4;
        $mode = ord($this->mode) & 0x0f;
        $binary[3] = $power | $mode;
        $binary[4] = ord($this->group);
        $hi = $this->frequency >> 8;
        $low = $this->frequency & 0x00ff;
        $binary[5] = $low;
        $binary[6] = $hi;
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
        $this->power = ord($data[3]) >> 4;
        $this->mode = ord($data[3]) & 0x0f;
        $this->group = ord($data[4]) & 0x0f;
        $this->frequency = ord($data[6]) << 8 | ord($data[5]);
        return $this->validate();
    }

}