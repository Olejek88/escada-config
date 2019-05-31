<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Common\components
 * @author   Олег <olejek8@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

namespace common\components;

class Errors
{
    /**
     * Перечень констант ошибок
     */
    const GENERAL_ERROR = -1;
    const OK = 0;
    const WRONG_INPUT_PARAMETERS = 1;
    const ERROR_SAVE = 2;
    const ERROR_GET_CLASS_ENTITY = 3;
    const ERROR_ALREADY_PRESENT = 4;
}

