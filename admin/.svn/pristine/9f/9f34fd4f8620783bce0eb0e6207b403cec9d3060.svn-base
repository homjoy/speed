<?php

namespace Admin\Package\Department;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户细心
 * @package Admin\Package\UserInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class Department extends \Admin\Package\Common\BasePackage {
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
    public  function getDepartInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/hacked_depart_info_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     * 部门信息与temp部门信息表
     */

    public  function getDepart($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_info_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getDepartTemp($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_info_temp_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateDepart($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateDepartTemp($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_info_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createDepart($params = array()){
        $ret = self::getClient()->call('atom', 'department/create_depart_info', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createDepartTemp($params = array()){
        $ret = self::getClient()->call('atom', 'department/create_depart_info_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createAllDepartInfoByTemp(){
        $push = self::getClient()->call('atom','department/create_all_depart_info_by_temp',array('token'=>'d1a7f6b8345f149f53be207e1d6d26a6'));
        $push = $this->parseRemoteData($push);
        return $push;
    }
    public  function backupAllDepartInfo(){
        $push_temp = self::getClient()->call('atom', 'department/backup_all_depart_info',array());
        $push_temp = $this->parseRemoteData($push_temp);
        return $push_temp;
    }
    public  function getLowerLevelList($params = array()){
        $ret = self::getClient()->call('atom', 'account/get_lower_level_user', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getAllDepartLeader($params = array()){
        $ret = self::getClient()->call('atom', 'department/get_all_depart_leader', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
