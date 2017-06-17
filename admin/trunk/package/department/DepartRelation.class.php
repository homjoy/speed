<?php

namespace Admin\Package\Department;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 部门Relation
 * @package Admin\Package\department\DepartRelation
 * @author hongzhou@meilishuo.com
 * @since 2015-11-25
 */

class DepartRelation extends \Admin\Package\Common\BasePackage {

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
     * 读取部门信息
     * @param type $params
     * @return type
     */
    public  function getRelationInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_relation_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function getRelationTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/depart_relation_temp_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function createRelationTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/create_depart_relation_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateRelationTempInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_relation_temp', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateRelationInfo($params = array()){
        $ret = self::getClient()->call('atom', 'department/update_depart_relation', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
