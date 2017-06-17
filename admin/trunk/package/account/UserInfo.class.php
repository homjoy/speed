<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户信息
 * @package Admin\Package\UserInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class UserInfo extends \Admin\Package\Common\BasePackage {
    //mail的发送地址
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
     * 读取用户信息
     * @param type $params
     * @return type
     */
    public  function getUserInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_user_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *搜索用户基本信息
     */
    public  function searchUserInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/search_user_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
    *创建用户基本信息
    */
    public  function createUserInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_user_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *更新用户基本信息
     */
    public  function updateUserInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/update_user_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


    /**
     * 获取最大的一个staffId
     */
    public  function getMaxStaffId($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_max_staff_id', $params);

        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
