<?php

namespace Admin\Package\Core;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取请假相关信息的package
 * @package Admin\Package\config
 * @author hongzhou@meilishuo.com
 * @since 2015-11-15
 */

class Config extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    public  function getChild($params = array()){
        $ret = self::getClient()->call('atom', 'core/config_get_child', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    public  function getValue($params = array()) {

        $ret = self::getClient()->call('atom', 'core/config_get_value', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;

    }
    public  function createConfig($params = array()){
        if(!is_array($params['value'])){
            $params['value'] = base64_encode($params['value']);
//                    var_dump($params);die();
        }
        $ret = self::getClient()->call('atom', 'core/config_create', $params);

        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function updateConfig($params = array()){
        if(!is_array($params['value'])){
            $params['value'] = base64_encode($params['value']);
        }
        $ret = self::getClient()->call('atom', 'core/config_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function configList($params = array()){
        $ret = self::getClient()->call('atom', 'core/config_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function configGet($params = array()){
        $ret = self::getClient()->call('atom', 'core/config_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    public  function configSearch($params = array()){
        $ret = self::getClient()->call('atom', 'core/config_search', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
