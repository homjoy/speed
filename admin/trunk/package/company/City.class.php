<?php

namespace Admin\Package\Company;

/**
 * 会议室通用方法
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class City extends \Admin\Package\Common\BasePackage {

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
     * 获取会议室的列表
     * @param array $params
     *
     * @return bool
     */
    public  function cityGet($params = array()){
        $ret = self::getClient()->call('atom', 'company/city_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function cityList($params = array()){
        $ret = self::getClient()->call('atom', 'company/city_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function cityCreate($params = array()){
        $ret = self::getClient()->call('atom', 'company/city_create', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function cityUpdate($params = array()){
        $ret = self::getClient()->call('atom', 'company/city_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
