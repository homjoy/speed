<?php

namespace Frame\Speed\Exception;
use Exception;

/**
 * 请求异常，跟请求相关的错误
 * Class ApiException
 * @package Frame\Speed\Exception
 */
class ApiException extends \Exception {

    protected $response = array();

    public function __construct($response,$message = "", $code = 0, Exception $previous = null)
    {
        if(empty($message) && isset($response['error_msg'])){
            $message =  (string)$response['error_msg'];
        }
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array|string
     */
    public function getResponse()
    {
        return $this->response;
    }
}
