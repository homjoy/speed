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

class UserOutsourcingInfo extends \Admin\Package\Common\BasePackage {
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
     *获取外包用户的信息
     */
    public  function searchUserOutsourcingInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_user_outsourcing_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateUserOutsourcingInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/update_user_outsourcing_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createUserOutsourcingInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_user_outsourcing_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 获取邮箱的后缀
     */
    public static  function get_mail_suffix($type){
        switch($type){
            case 1: return '@meilishuo.com';
            case 2: return '@kf.meilishuo.com';
            default:
                return '@meilishuo.com';
        }
    }

    /**
     * 通过邮箱后缀,返回类型
     */
    public static function get_mail_suffix_type($mail_suffix){
        switch($mail_suffix){
            case 'meilishuo.com': return 1;
            case 'kf.meilishuo.com': return 2;
            default:
                return 2;
        }
    }
}
