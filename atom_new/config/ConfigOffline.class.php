<?php

namespace Atom\Config;

class ConfigOffline {

    public static function instance() {
        static $c = null;
        is_null($c) && $c = new self();
        return $c;
    }

    private function __construct() {}

    public static function db() {
        $databases = array(
            'dolphin' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
                )
            ),
            'shark' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => 'dolphin'),
                )
            ),
//            'dolphin' => array(
//                'MASTER' => array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => 'dolphin'),
//                    array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => 'dolphin'),
//                )
//            ),
//            'shark' => array(
//                'MASTER' => array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => 'dolphin'),
//                'SLAVES' => array(
//                    array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => 'dolphin'),
//                )
//            ),
        );

        /*
         * 生成数据库配置.
         */
        $list = array('core','administration','hr','recruit','routine','staff','worker','crab','approval');
        foreach($list as $i=>$value){
            $db = 'speed_'.$value;
            if($value == 'crab'){
                $db = $value;
            }
            $databases[$db] = array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => $db),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => $db),
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'mlstmpdb', 'PASS' => 'mlstmpdb123456', 'DB' => $db),
                )
//                'MASTER' => array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => $db),
//                'SLAVES' => array(
//                array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => $db),
//                array('HOST' => '10.8.255.72', 'PORT' => '3306', 'USER' => 'speed', 'PASS' => 'speed-rd', 'DB' => $db),
//            )
            );
        }
        
        return $databases;
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

    public function memcache() {
        return array(
            'unixsock' => array(
                array('host' => '10.8.3.55', 'port' => 11211),
//                array('host' => '10.8.255.72', 'port' => 11211),
            )
        );
    }

    public function remote() {
        return array(
            'atom' => array(
                'http://atom.newspeed.meilishuo.com',
                'http://atom.newspeed.meilishuo.com',
            ),
            'worker' => array(
                'http://worker.newspeed.meilishuo.com',
                'http://worker.newspeed.meilishuo.com',
            )
        );
    }

    /**
     * 短信发送的接口.
     * @return array
     */
    public function smsApi()
    {
        return array(
            'method' => 'GET',
            'url' => 'http://127.0.0.1/notification/sms.php?', //本机模拟的接口.
        );
    }

    /**
     * 邮件发送的接口.
     * @return array
     */
    public function mailApi()
    {
        return array(
            'method' => 'POST',
            'url' => 'http://127.0.0.1/notification/mail.php', //本机模拟的接口.
        );
    }

    /**
     * IM 发送的接口.
     * @return array
     */
    public function imApi()
    {
        return array(
            'method' => 'GET',
            'url' => 'http://127.0.0.1/notification/im.php', //本机模拟的接口.
        );
    }
}
