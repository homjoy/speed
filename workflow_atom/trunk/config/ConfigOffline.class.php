<?php

namespace Workflow_atom\Config;

class ConfigOffline {

    public static function instance() {
        static $c = null;
        is_null($c) && $c = new self();
        return $c;
    }

    private function __construct() {}

    public function db() {
        $databases = array(
            'dolphin' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'mysql'),
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'mysql'),
                )
            ),
            'shark' => array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'mysql'),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => 'mysql'),
                )
            ),
            'workflow' => array(
                'MASTER' => array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'workflow'),
                'SLAVES' => array(
                    array('HOST' => '127.0.0.1', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'newpass', 'DB' => 'workflow'),
                )
            ),
        );

        /*
         * 生成数据库配置.
         */
        $list = array('core','administration','hr','recruit','routine','staff','worker','crab');
        foreach($list as $i=>$value){
            $db = 'speed_'.$value;
            if($value == 'crab'){
                $db = $value;
            }
            $databases[$db] = array(
                'MASTER' => array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => $db),
                'SLAVES' => array(
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => $db),
                    array('HOST' => '10.8.3.55', 'PORT' => '3306', 'USER' => 'dolphin', 'PASS' => 'dolphin', 'DB' => $db),
                )
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
                array('host' => '127.0.0.1', 'port' => 11211),
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
