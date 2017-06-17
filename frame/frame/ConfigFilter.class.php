<?php

namespace Frame;

class ConfigFilter
{
    private $app;

    final public static function instance() {
        static $cf = null;
        is_null($cf) && $cf = new ConfigFilter();
        return $cf;
    }

    private function __construct() {}

    final public function setApplication($app) {
        $this->app = $app;
    }

    public function getConfig($name) {
        return $this->app->config->$name;
    }

}
