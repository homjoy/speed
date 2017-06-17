<?php

namespace Admin\Package\Company;

/**
 * 会议室通用方法
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class Office extends \Admin\Package\Common\BasePackage {

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
    public  function officeList($params = array()){
        $ret = self::getClient()->call('atom', 'company/office_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 获取会议信息
     * @param array $params
     *
     * @return bool
     */
    public function officeGet($params = array()){
        $ret = self::getClient()->call('atom', 'company/office_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


}
