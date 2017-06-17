<?php

namespace Admin\Package\Company;

/**
 * 会议室通用方法
 * @package Admin\Package\company
 * @author hongzhou@meilishuo.com
 * @since 2015-11-05
 */

class company extends \Admin\Package\Common\BasePackage {

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
     * 获取会议室地区列表
     * @param array $params
     *
     * @return bool
     */
    public  function companyList($params = array()){
        $ret = self::getClient()->call('atom', 'company/company_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 获取会议信息
     * @param array $params
     *
     * @return bool
     */
    public function companyGet($params = array()){
        $ret = self::getClient()->call('atom', 'company/company_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    public function companyUpdate($params = array()){
        $ret = self::getClient()->call('atom', 'company/company_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public function companyCreate($params = array()){
        $ret = self::getClient()->call('atom', 'company/company_create', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
