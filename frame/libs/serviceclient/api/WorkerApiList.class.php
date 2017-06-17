<?php

namespace Libs\Serviceclient\Api;

/**
 * worker 接口配置.
 */
class WorkerApiList extends \Libs\Serviceclient\Api\ApiList {

    /**
     * @var array
     */
    protected static $apiList = array(
        //通知推送.
        'notification/push' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notification/push_all' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notification/status_changed' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notification/create_pusher' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notification/message_delete' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notification/message_delete_batch' => array('service' => 'worker', 'method' => 'POST', 'opt' => array('timeout' => 3)),
    );

}
