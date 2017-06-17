<?php

namespace Frame\Speed\Exception;
use Exception;

/**
 * 断言异常
 *
 * Class AssertException
 * @package Frame\Speed\Exception
 */
class AssertException extends \Exception {

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
