<?php

namespace Worker\Config;

class ConfigOffline {

    public static function instance() {
        static $c = null;
        is_null($c) && $c = new self();
        return $c;
    }

    private function __construct() {}

    public function db() {
        $databases = array();
        /*
         * 生成数据库配置.
         */
        $list = array('recruit','worker');
        foreach($list as $i=>$value){
            $db = 'speed_'.$value;
            $databases[$db] = array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'mysql'),
                )
            );
        }

        return $databases;
    }

    public function redis() {
        return array(
		    'writeHost' => 'http://127.0.0.1:6379/write',
		    'xwriteHost' => 'http://127.0.0.1:6379/xwrite',
            'readHosts' => array(
                'http://127.0.0.1:6379/read',
                'http://127.0.0.1:6379/read',
                'http://127.0.0.1:6379/read'
            )
        );
    }

    public function remote() {
        return array(
            'atom' => array(
                'http://atom.testnewspeed.meilishuo.com',
                'http://atom.testnewspeed.meilishuo.com',
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
