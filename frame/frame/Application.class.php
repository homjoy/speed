<?php

namespace Frame;

class Application {

    public $middleware = array();
    public $container;

    public static function instance() {
        static $app = null;
        is_null($app) && $app = new Application();
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
        $this->push(new \Frame\Middleware\PrettyExceptionHandler());
        $this->push(new \Frame\Middleware\PrettyResultHandler());

    }

    public function __get($name)
    {
        return $this->container[$name];
    }

    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }

    public function __call($method, $args)
    {
        call_user_func_array(array($this->container, $method), $args);
    }

    public function push(Middleware $newMiddleware)
    {
        $newMiddleware->setApplication($this);
        $newMiddleware->setNextMiddleware($this->middleware[0]);
        array_unshift($this->middleware, $newMiddleware);
    }

    public function call()
    {
        $class = $this->router->dispatch();
        $class = $this->checkClass($class);
        $instance = new $class($this);
        $pClass = ('cli' == $this->sapi_type) ? '\\Frame\\Script' : '\\Frame\\Module';
        if (!$instance instanceof $pClass) {
            throw new \Exception("{$class} is not the instance of module class");
        }
        $instance->start();
    }

    public function run()
    {
        $this->setRequireMent();
        $this->middleware[0]->call();
    }

    private function setRequireMent() {
        //instance cnnfig filter
        ConfigFilter::instance()->setApplication($this); 
        //log
        $this->container->singleton('log', function ($c) {
            $logWriter = is_object($c['logWriter']) ? $c['logWriter'] : new \Libs\Log\BasicLogWriter();
            return new \Libs\Log\Log($logWriter);
        });
    }

    private function checkClass($class){
        if (!class_exists($class)) {

            $array = explode("\\",$class);
            $array[3] = 'Bad';
            $array[4] = 'Badrequest';

            $class = implode("\\", $array);
        }
        return $class;
    }

}
