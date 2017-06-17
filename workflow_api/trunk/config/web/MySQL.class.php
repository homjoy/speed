<?php

namespace WorkFlowApi\Config\Web;

//require_once '/home/work/conf/api/MySQLConfigApi.php';

class MySQL extends \Frame\Config {

    const MYSQL_KEY = 'speed';

    public static function config() {
    	$config = array(
            'root' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            ),
            'workflow' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'workflow'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'workflow'),
                )
            ),
        );

    	return $config;
 //       return \MySQLConfigApi::GetCfgByServKey(self::MYSQL_KEY);
    }

}

