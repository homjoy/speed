<?php

namespace Libs\Router;

class BasicScriptsRouter {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function dispatch() {
        $args = $this->app->request->arg;
		$name = array_shift($args);

        $scripts_namespace = $this->app->scripts_namespace;

		$class = $scripts_namespace . ucwords($name);
        return $class;
    }

}
