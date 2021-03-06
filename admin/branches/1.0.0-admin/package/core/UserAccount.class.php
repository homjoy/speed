<?php

namespace Admin\Package\Core;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 *
 * @package Admin\Package\Core
 * @author hongzhou@meilishuo.com
 * @since 2015-11-15
 */

class UserAccount extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    public  function createAccount($params = array()){
        $ret = self::getClient()->call('atom', 'core/add_mail_password_time', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getAccount($params = array()){
        $ret = self::getClient()->call('atom', 'core/get_mail_password_time', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateAccount($params = array()){
        $ret = self::getClient()->call('atom', 'core/update_mail_password_time', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
