<?php

namespace Frame\Speed\Middleware;

use Frame\Exception\SQLExecException;
use Frame\Speed\Exception\AuthException;
use Frame\Speed\Exception\DatabaseException;
use Frame\Speed\Exception\ParameterException;
use Frame\Speed\Exception\RequestException;

/**
 * Class ExceptionHandler
 * @package Frame\Speed\Middleware
 */
class ExceptionHandler extends \Frame\Middleware
{
    /**
     * @var array
     */
    protected $settings;

    public function __construct($settings = array()) {}

    /**
     * Call
     */
    public function call()
    {
        try {
            $this->next->call();
        } catch (\Exception $e) {
            $response = array();
            $response['code'] = 500;
            $response['error_code'] = $e->getCode();
            $response['error_msg'] = $e->getMessage();

            //请求错误
            if($e instanceof RequestException){
                $response['code'] = 400;
            }
//            //认证错误
//            if($e instanceof AuthException){
//                $response['code'] = 400;
//            }
            //请求参数错误
            if($e instanceof ParameterException){
                $response['code'] = 400;
                $response['error_detail'] = $e->getErrors();
            }

            //数据库错误
            if($e instanceof SQLExecException or $e instanceof DatabaseException){
//                $this->app->log->log('exception', $e->getMessage());
                $this->app->log->log('db_error', array(
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ));
                $this->app->response->setStatus(500);
                $response['code'] = 500;
                $response['error_msg'] ='数据库异常！';
            }

            $this->app->response->setBody($response,true);
        }
    }

}
