<?php

namespace common\models\mtm;

/**
 *
 * @property mixed $base64Data
 * @property array $binaryData
 */
class MtmContactor extends MtmPktHeader
{
    // линия управляющая реле (у на пока одна - 7)
    public $line;
    // состояние реле - 0 выключено, 1 включено
    public $state;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->type = self::$MTM_CMD_TYPE_CONTACTOR;
        $this->line = 7;
        $this->state = 0;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['line'], 'in', 'range' => [7]],
            [['state'], 'integer', 'min' => 0, 'max' => 1],
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
        $binary[3] = $this->line;
        $binary[4] = $this->state;
        return $binary;
    }
}