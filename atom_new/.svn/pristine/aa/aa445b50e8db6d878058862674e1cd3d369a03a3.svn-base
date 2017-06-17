<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Approval\OrderLeave;
use Atom\Package\Approval\OrderAttachment;

/**
 * 同步老附件信息
 * Class SyncOldSpeedLeaveInfo
 * @package Atom\Scripts\Sync
 */
class SyncOrderAttachment extends \Frame\Script{
    public function run(){

        //同步请假信息.
        $this->getInfo(0,20000);

        return $this->response->setBody("同步结束.\n");
    }

    private function getInfo(){
        $data = OrderLeave::model()->getAttachment();

        $params = array();
        foreach($data as $value){
            $local = json_decode($value['local_leave_file'],true);
            $service = json_decode($value['service_leave_file'],true);
            $params['order_id'] = $value['order_id'];
            $params['order_type'] = 1;
            foreach($local as $k=>$v){
                $params['service_file'] = isset($service[$k]) ? $service[$k] : '';
                $params['local_file'] = $v;

                OrderAttachment::model()->insert($params);
            }
        }
    }
}
