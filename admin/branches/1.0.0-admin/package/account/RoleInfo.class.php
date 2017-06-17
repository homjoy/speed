<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户角色信息
 * @package Admin\Package\Account\RoleInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class RoleInfo extends \Admin\Package\Common\BasePackage {
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
     * 读取用户角色信息
     */
    public  function getRoleInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/user_job_role_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


}
