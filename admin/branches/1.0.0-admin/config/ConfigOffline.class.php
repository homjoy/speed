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
//            'dolphin' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
//                )
//            ),
//            'shark' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
//                )
//            ),
//            'vote' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
//                )
//            ),
//			'workflow' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'workflow'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'workflow'),
//                )
//            ),
//            'speed_administration' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'speed_administration'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'speed_administration'),
//                )
//            ),
            'dolphin' => array(
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
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            ),
            'workflow' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            ),
			/*'workflow' => array(
                'MASTER' => array('HOST' => 'localhost', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'root', 'DB' => 'workflow'),
                'SLAVES' => array(
                    array('HOST' => 'localhost', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'root', 'DB' => 'workflow'),
                )
            ),*/
            // 'vote' => array(
            //     'MASTER' => array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
            //     'SLAVES' => array(
            //         array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'vote'),
            //     )
            // ),
        );
    }

    public function redis() {
        return array(
                    'writeHost' => 'http://127.0.0.1:6379/write',
                    'xwriteHost' => 'http://127.0.0.1:6379/xwrite',
                    'readHosts' => array(
                        'http://127.0.0.1:6379/read',
                )
        );
    }
    
    public function memcache() {
        return array(
        	'unixsock' => array(
	        	array('host' => '127.0.0.1' , 'port' => '11211'),
                array('host' => '/home/work/nutcracker/twemproxy2' , 'port' => '0'),
       	 	)
        );
    }


}
