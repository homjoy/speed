<?php

namespace Frame\Middleware;

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
