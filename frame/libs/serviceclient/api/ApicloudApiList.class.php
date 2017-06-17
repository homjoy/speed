<?php

namespace Libs\Serviceclient\Api;

/**
 * 记录api的请求方式和服务模块
 * @author zx
 */
class ApicloudApiList extends \Libs\Serviceclient\Api\ApiList {

    protected static $apiList = array(

        //登录
        'auth/login' => array('service' => 'apicloud', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //获取蘑菇街数据
        'auth/get_mgj_user_info' => array('service' => 'apicloud', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //退出
        'auth/logout' => array('service' => 'apicloud', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    );

}
