<?php

namespace common\models\mtm;

class MtmDevLightStatus extends MtmPktHeader
{
    const MAX_SENSORS = 16;

    public $mac;
    public $alert;
    public $data;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mac', 'string', 'length' => [16]],
            ['alert', 'integer', 'min' => 0x00, 'max' => 0xffff],
//            ['data', 'each', 'rule' => ['integer', 'min' => 0x00, 'max' => 0xffff]],
//            ['data', 'each', 'rule' => ['checkDataSize']],
            ['data', 'checkDataSize'],
        ];
    }

    public function checkDataSize($attr, $param)
    {
        $statusValues = $this->attributes[$attr];
        if (!is_array($statusValues)) {
            $this->addError($attr, 'Должен быть список элементов ');
            return;
        }

        $count = count($statusValues);
        if ($count == 0 || $count > 16) {
            $this->addError($attr, 'Список элементов должен быть больше 0 и меньше 17');
            return;
        }
    }

    public function loadBase64Data($data)
    {
        $this->loadBinaryData(base64_decode($data));
    }

    public function loadBinaryData($data)
    {
        $dataLen = strlen($data);
        // таким нехитрым способом определяем сколько на самом деле двух байтовых значений статусов датчиков
        // пришло в пакете со светильника (12 = 1 тип + 1 версия + 8 mac + 2 alert)
        $sensorsCount = $dataLen - 12;
        if ($sensorsCount % 2 != 0) {
            $this->addError('sensors_count', 'Не чётное значение байт статусов датчиков.');
            return false;
        } else {
            $sensorsCount = $sensorsCount / 2;
            if ($sensorsCount >= self::MAX_SENSORS) {
                $this->addError('sensors_count', 'Количество статусов датчиков больше ' . self::MAX_SENSORS);
                return false;
            }
        }

        $this->type = ord($data[0]);
        $this->protoVersion = ord($data[1]);
        $this->mac =
            self::i2h(ord($data[9])) .
            self::i2h(ord($data[8])) .
            self::i2h(ord($data[7])) .
            self::i2h(ord($data[6])) .
            self::i2h(ord($data[5])) .
            self::i2h(ord($data[4])) .
            self::i2h(ord($data[3])) .
            self::i2h(ord($data[2]));
        $this->alert = ord($data[10]) | (ord($data[11]) << 8);

        for ($i = 0; $i < $sensorsCount; $i++) {
            $this->data[$i] = ord($data[$i * 2 + 12]) | (ord($data[$i * 2 + 13]) << 8);
        }

        return $this->validate();
    }

    public static function i2h($int)
    {
        return $int < 16 ? '0' . dechex($int) : dechex($int);
    }

}