<?php

namespace Aivo\Exceptions;

class DatabaseError extends \Aivo\BaseException
{
    /**
     * @var string
     */
    public $message = 'Database error.';

    /**
     * @var int
     */
    public $code = 1;

    /**
     * @var
     */
    public $level = self::CRITICAL;

    /**
     * @var int
     */
    public $httpCode = 500;

    /**
     * @var string
     */
    public $class = __CLASS__;

}
