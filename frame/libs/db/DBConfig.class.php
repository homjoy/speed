<?php

namespace Libs\DB;

class DBConfig {

    private $configs;

    public static function instance() {
        static $cf = null;
        is_null($cf) && $cf = new DBConfig();
        return $cf;
    }

    private function __construct() {
        $this->configs = \Frame\ConfigFilter::instance()->getConfig('db');
    }

    public function loadConfig($database, $type) {
        switch ($type) {
            case 'MASTER':
            return new \ArrayIterator(array($this->configs[$database][$type]));
            default:
            return new \ArrayIterator(\Frame\Helper\Util::ssort($this->configs[$database][$type]));
        }
    }


}
