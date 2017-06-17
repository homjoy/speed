<?php

namespace Frame\Middleware;
use Frame\Exception\AdminAuthException;
use Frame\Exception\UrlCheckException;

class PrettyExceptionHandler extends \Frame\Middleware
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
            //$this->app->log->log('exception', $e->getMessage());
            if($e instanceof AdminAuthException){
                $this->app->log->log('auth_exception', array(
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ));

                // 输出没有操作权限的错误信息
                $this->app->response->setBody(array(
                    'code'      => 400,
                    'error_msg' => '没有操作权限',
                ));
            } else if ($e instanceof UrlCheckException) {
                $this->app->log->log('url_exception', array(
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ));

                // 输出没有操作权限的错误信息
                $this->app->response->setBody(array(
                    'code'      => 400,
                    'error_msg' => '指定模块不存在',
                ));
            } else {
                $this->app->log->log('exception', array(
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ));
                //输出httpcode500
                $this->app->response->setStatus(500);
            } 
        }
    }

}
