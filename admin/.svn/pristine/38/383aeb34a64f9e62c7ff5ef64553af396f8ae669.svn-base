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
            'vote' => array(
                'MASTER' => array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'vote'),
                'SLAVES' => array(
                    array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'vote'),
                )
            ),
			'workflow' => array(
                'MASTER' => array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'workflow'),
                'SLAVES' => array(
                    array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'workflow'),
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
