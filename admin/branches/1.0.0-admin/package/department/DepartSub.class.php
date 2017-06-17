<?php

namespace Admin\Package\Department;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 部门Sub
 * @package Admin\Package\department\DepartSub
 * @author hongzhou@meilishuo.com
 * @since 2015-11-05
 */

class DepartSub extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
          
        }
        return self::$instance;
    }


    public  function getSubInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_sub_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getSubTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_sub_temp_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createSubTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/create_depart_sub_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createSubInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/create_depart_sub', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateSubTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_sub_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateSubInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_sub', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    

}
