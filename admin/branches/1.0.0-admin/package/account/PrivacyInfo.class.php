<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户私密信息
 * @package Admin\Package\Account\PrivacyInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-11-25
 */

class PrivacyInfo extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
          
        }
        return self::$instance;
    }


    /**
     * 读取用户私密信息
     */
    public  function getPrivacyInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_privacy_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /*
    *创建用户私密信息
    */
    public  function createPrivacyInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_privacy_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *更新用户私密信息
     */
    public  function updatePrivacyInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/update_privacy_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
