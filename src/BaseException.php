<?php

namespace Aivo;

use \Psr\Log\LoggerInterface;

abstract class BaseException extends \Exception implements
    \Psr\Log\LoggerAwareInterface,
    \JsonSerializable
{
    /**
     * @var string
     */
    const EMERGENCY = 'emergency';

    /**
     * @var string
     */
    const ALERT = 'alert';

    /**
     * @var string
     */
    const CRITICAL = 'critical';

    /**
     * @var string
     */
    const ERROR = 'error';

    /**
     * @var string
     */
    const WARNING = 'warning';

    /**
     * @var string
     */
    const NOTICE = 'notice';

    /**
     * @var string
     */
    const INFO = 'info';

    /**
     * @var string
     */
    const DEBUG = 'debug';

    /**
     * @var string
     */
    protected $message = 'Unknown error.';

    /**
     * @var int
     */
    protected $code = 0;

    /**
     * @var
     */
    protected $level = self::CRITICAL;

    /**
     * @var int
     */
    protected $httpCode = 500;

    /**
     * @var string
     */
    protected $class = __CLASS__;

    /**
     * @var array|object
     */
    protected $data = [];

    /**
     * @var \Exception
     */
    protected $previous = null;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Exception|\Throwable    $previous
     * @param string                   $previousLevel
     *
     * @return \Aivo\BaseException
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger = null,
        $previous = null,
        $previousLevel = null)
    {
        $this->setLogger($logger);
        $this->setPrevious($previous);

        parent::__construct($this->message, $this->code, $previous);

        $this->logThis();
        if (!empty($previousLevel)) {
            $previousLevel = strtolower($previousLevel);
            $this->log($previous->getMessage(), $previousLevel);
        }
    }

    /**
     * Tries to call the PSR-3
     *
     * @param string $message
     * @param string $level   PSR-3 valid level
     */
    public function log($message, $level, $data = [])
    {
        if (!empty($this->logger)) {
            $level = strtolower($level);

//            if (empty($data)) {
                $this->logger->$level($message, $data);
//            } else {
//                $this->logger->$level($message);
//            }
        }
    }

    /**
     * Calls to $this->log using this instance's values
     */
    public function logThis()
    {
        $this->log($this->message, $this->level, $this->data);
    }

    // Message

    /**
     * @param string $message
     *
     * @return BaseException
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;

        return $this;
    }

    // Code

    /**
     * @param int $code
     *
     * @return BaseException
     */
    public function setCode($code)
    {
        $this->code = (int) $code;

        return $this;
    }

    // Class

    /**
     * @return string
     */
    public function getClass()
    {
        if (empty($this->exceptionClass)) {
            return get_class($this);
        }

        return $this->exceptionClass;
    }

    /**
     * @param int $code
     *
     * @return BaseException
     */
    public function setClass($class = null)
    {
        $this->exceptionClass = $class ?: __CLASS__;

        return $this;
    }

    // Level

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $code
     *
     * @return BaseException
     */
    public function setLevel($level = null)
    {
        $this->level = $level ?: self::ERROR;

        return $this;
    }

    // Http Code

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     *
     * @return BaseException
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    // Data

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object $data
     *
     * @return BaseException
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    // Logger

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return BaseException
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Exception $code
     *
     * @return BaseException
     */
    public function setPrevious($e)
    {
        if ($e instanceof \Throwable || $e instanceof \Exception) {
            $this->previous = $e;
        }

        return $this;
    }

    public function __toArray()
    {
        $return = [
            'message' => $this->getMessage(),
            'class' => $this->getClass(),
            'code' => $this->getCode(),
        ];
        if (!empty($this->data)) {
            $return['data'] = $this->getData();
        }

        return $return;
    }

    public function jsonSerialize()
    {
        return $this->__toArray();
    }
}
