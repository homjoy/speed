<?php

namespace Demo;

class ConfigDemo {

    public static function instance() {
        static $c = null;
        is_null($c) && $c = new ConfigDemo();
        return $c;
    }

    private function __construct() {}

    public function db() {
        return array(
            'dolphin' => array(
                'MASTER' => array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                    array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                )
            ),
            'shark' => array(
                'MASTER' => array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                )
            ),
        );
    }

    public function redis() {
        return array(
		    'writeHost' => 'http://192.168.128.12/write',
		    'xwriteHost' => 'http://192.168.128.12/xwrite',
            'readHosts' => array(
                'http://192.168.128.12:8080/read',
                'http://192.168.128.12:8080/read',
                'http://192.168.128.12:8080/read'
            )
        );
    }

}
