<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户私人信息
 * @package Admin\Package\Account\PersonalInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-11-15
 */

class PersonalInfo extends \Admin\Package\Common\BasePackage {

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
     * 读取用户私人信息
     */
    public  function getPersonalInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_personal_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /*
    *创建用户私人信息
    */
    public  function createPersonalInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_personal_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *更新用户私人信息
     */
    public  function updatePersonalInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/update_personal_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *搜索用户私人信息
     */
    public  function searchPersonalInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/search_personal_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
