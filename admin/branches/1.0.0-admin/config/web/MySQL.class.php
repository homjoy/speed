<?php

namespace Monster\Config\Web;

require_once '/home/work/conf/api/MySQLConfigApi.php';

class MySQL extends \Frame\Config {

    const MYSQL_KEY = 'online';

    public function config($name='vote') {
        $config = array(
//            'workflow' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
//                )
//            ),
//            'shark' => array(
//                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
//                )
//            ),
            'workflow' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                )
            ),
            'shark' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'dolphin'),
                )
            ),
        );

        return $config[$name];
    }

    

}


