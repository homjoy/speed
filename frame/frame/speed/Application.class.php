<?php

namespace Frame\Speed;

use Frame\Middleware;

class Application extends \Frame\Application{

    /**
     * @return static
     */
    public static function instance() {
        static $app = null;
        is_null($app) && $app = new static();
        return $app;
    }

    private function __construct() {
        $this->container = new \Frame\Helper\Set();

        $this->container->sapi_type = php_sapi_name();
        $this->container->singleton('config', function ($c) {
             return new \Frame\Config();
        });

        //middleware
        $this->middleware = array($this);
        $this->push(new \Frame\Speed\Middleware\ExceptionHandler());
//        $this->push(new \Frame\Middleware\PrettyExceptionHandler());
        $this->push(new \Frame\Middleware\PrettyResultHandler());
    }

    /**
     * push before
     * @param Middleware $newMiddleware
     * @param null $before
     */
    public function push(Middleware $newMiddleware,$before = null)
    {
        $newMiddleware->setApplication($this);
        if(!is_null($before)){
            foreach($this->middleware as $i=>$middleware){
                if($middleware instanceof $before){
                    $before = $this->middleware[$i];
                    $next = $this->middleware[$i+1];
                    $before->setNextMiddleware($newMiddleware);
                    $newMiddleware->setNextMiddleware($next);
                    array_splice($this->middleware,$i+1,0,array($newMiddleware));
                    break;
                }
            }
        }else{
            $newMiddleware->setNextMiddleware($this->middleware[0]);
            array_unshift($this->middleware, $newMiddleware);
        }
    }

}
