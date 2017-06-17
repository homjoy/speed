<?php
namespace Libs\Serviceclient;

class ServiceApiInfomation {

    private static function getApiInfo($server, $api = '') {
        if (empty($server)) {
            $server = 'atom';
        }
        $api = strtolower($api);
        $api = trim($api, '/');
        $className = __NAMESPACE__ . '\\Api\\' . ucfirst($server) . 'ApiList';
        if (class_exists($className)) {
            return call_user_func_array(array($className, 'get'), array($server, $api));
            //return \Snake\Libs\Serviceclient\ApiList::get($server, $api);
        }
        else {
            return array();
        }
    }

    public static function getApiMethod($server, $api = '') {
        $apiInfo = self::getApiInfo($server, $api);
        return strtoupper($apiInfo['method']);
    }

    public static function getApiUrl($server, $api) {
        return self::getServiceHost($server) . '/' . ltrim($api, '/');
    }

    public static function getApiOpt($server, $api = '') {
        $apiInfo = self::getApiInfo($server, $api);
        return isset($apiInfo)&& isset($apiInfo['opt'])?$apiInfo['opt']:'';
    }

    private static function getServiceHost($remote) {
        $config = \Frame\ConfigFilter::instance()->getConfig('remote');
        $hosts = NULL;
        if (isset($config[$remote])) {
            $hosts = $config[$remote];
        }
        if(is_array($hosts)){
            return $hosts[array_rand($hosts)];
        }else{
            return $hosts;
        }
    }
}
