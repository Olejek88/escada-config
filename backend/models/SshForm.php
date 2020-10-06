<?php

namespace backend\models;

use yii\base\Model;

/**
 * Ssh form
 */
class SshForm extends Model
{
    public $localPort;
    public $bindIp;
    public $remotePort;
    public $remoteHost;
    public $user;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'localPort',
                    'bindIp',
                    'remotePort',
                    'remoteHost',
                    'user',
                    'password',
                ],
                'required'
            ]
        ];
    }
}
