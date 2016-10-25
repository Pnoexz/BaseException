<?php

namespace Aivo\Exceptions\Word;

class NotFound extends \Aivo\BaseException
{
    /**
     * @var string
     */
    public $message = 'Not found';

    /**
     * @var int
     */
    public $code = 7;

    /**
     * @var string
     */
    public $level = self::ERROR;

    /**
     * @var int
     */
    public $httpCode = 404;

    /**
     * @var string
     */
    public $class = __CLASS__;

}
