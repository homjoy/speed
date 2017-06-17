<?php
namespace Frame\Speed\Proxy;

class BasicRouter {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function dispatch() {

        $module_namespace = $this->app->module_namespace;
		$class = $module_namespace . 'Proxy' . '\\' . 'Request';
        return $class;
    }

}
