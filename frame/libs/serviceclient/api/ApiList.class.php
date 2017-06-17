<?php
namespace Libs\Serviceclient\Api;

/**
 * 记录api的请求方式和服务模块
 * @author zx
 */

class ApiList {
    private static $apiList = array(
        //'goodslist/get_goods_list' => array('service' => 'beaver', 'method' => 'POST'),
    );

    public static function get($server, $api) {
        // $apiInfo = array('service' => 'virus', 'method' => 'POST', 'opt' => array('timeout' => 3));
        $apiInfo = array('service' => 'atom', 'method' => 'POST');
        if (isset(static::$apiList[$api])) {
            $apiInfo = static::$apiList[$api];
        }
        return $apiInfo;
    }
}
