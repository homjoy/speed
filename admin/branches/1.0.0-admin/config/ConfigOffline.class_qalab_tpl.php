<?php

namespace Admin\Config;

class ConfigOffline {

    public static function instance() {
        static $c = null;
        is_null($c) && $c = new self();
        return $c;
    }

    private function __construct() {}

    public function db() {
        return array(
//            'mysql' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'mysql'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'mysql'),
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'mysql'),
//                )
//            ),
//            'shark' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'mysql'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'mysql'),
//                )
//            ),
            'mysql' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            ),
            'shark' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            ),
            'vote' => array(
                'MASTER' => array('HOST' => '10.6.3.91', 'PORT' => '3336', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
                'SLAVES' => array(
                    array('HOST' => '10.6.3.91', 'PORT' => '3336', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
                )
            ),
        );
    }

    public function redis() {
        return array(
                    'writeHost' => 'http://10.6.4.179:8081/write',
                    'xwriteHost' => 'http://10.6.4.179:8081/xwrite',
                    'readHosts' => array(
                        'http://10.6.4.179:8081/read',
                )
        );
    }
    
    public function memcache() {
        return array(
            'unixsock' => array(
                array('host' => '/home/work/nutcracker/twemproxy1' , 'port' => '0'),
                array('host' => '/home/work/nutcracker/twemproxy2' , 'port' => '0'),
            )
        );
    }


}
