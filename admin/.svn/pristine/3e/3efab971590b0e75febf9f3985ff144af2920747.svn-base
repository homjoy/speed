<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户信息
 * @package Admin\Package\Account\WorkInfo
 * @author hongzhou@meilishuo.com
 * @since 2015-11-15
 */

class WorkInfo extends \Admin\Package\Common\BasePackage {

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
     * 读取用户工作信息
     */
    public  function getWorkInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_work_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /*
    *创建用户工作信息
    */
    public  function createWorkInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_work_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *更新用户工作信息
     */
    public  function updateWorkInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/update_work_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
