<?php

namespace Frame;

class Config
{
    
    private $configs;

    public function __construct() {
        $this->configs = new \Frame\Helper\Set();
    }

    public function __set($key, $value) {
        if (!is_callable($value)) {
            throw new \InvalidArgumentException("{$key} in config must be a closure");
        }
        $this->set($key, $value);
    }

    public function __get($key) {
        return $this->configs[$key];
    }

    private function set($key, $value) {
        $this->configs->singleton($key, $value);
    }

}
