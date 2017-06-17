<?php

namespace Frame\Speed\Exception;
use Exception;

/**
 * 参数异常
 *
 * 请求的参数未提供、格式不正确等
 * Class ParameterException
 * @package Frame\Speed\Exception
 */
class ParameterException extends \Exception {

    protected $errors = array();

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function __construct($message = "", $code = 0, $errors = array())
    {
        parent::__construct($message, $code, null);
        $this->errors = $errors;
    }
}
