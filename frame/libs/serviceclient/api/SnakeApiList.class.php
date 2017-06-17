<?php

namespace Libs\Serviceclient\Api;

class SnakeApiList extends \Libs\Serviceclient\Api\ApiList {

    protected static $apiList = array(
        'goods/Parallel_goods_verify' =>
        array('service' => 'snake', 'method' => 'POST', 'opt' => array('timeout' => 1)),
        'goods/Parallel_commerce_goods' =>
        array('service' => 'snake', 'method' => 'POST', 'opt' => array('timeout' => 1)),
    );

}
