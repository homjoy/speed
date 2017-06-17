<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Migrate\Crab;
use Atom\Package\Approval\OrderOfficeSupply;
use Atom\Package\Approval\OrderOfficeDetail;
use Frame\Speed\Lib\Api;
/**
 * 同步老SPEED 办公用品申请
 * Class SyncOldSpeedOfficeSupplies
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedOfficeSupply extends \Frame\Script{

    public function run(){

        //同步办公用品流程.
        $this->syncInfo();

        return $this->response->setBody("同步结束.\n");
    }

    private function syncInfo(){
        $p = array(
            'doc_status' => array(0,1,2),
            'doc_type'  => 2,
        );
        $document = Crab::model()->getDocumentInfo($p);

        $params = array();
        foreach($document as $value){
            switch($value['doc_status']){
                case 0:            //审批中
                    $params['status'] = 3;
                    $params['output'] = 2;//未发放
                    break;
                case 1:             //审批通过
                    $params['status'] = 4;
                    $params['output'] = 1;//发放
                    break;
                case 2:             //审批驳回
                    $params['status'] = 5;
                    $params['output'] = 2;//未发放
                    break;
                case 7:
                    $params['status'] = 0;
                    $params['output'] = 2;//未发放
                    break;
            }

            $params['order_id'] = $value['doc_id'];
            $params['task_id'] = 0;
            $params['user_id'] = $value['user_id'];
            $params['update_time'] = $value['create_time'];
            $params['create_time'] = $value['create_time'];

            $params['parent_id']    = $value['doc_id'];
            $params['order_type']   = 1;

            $others = json_decode($value['doc_data_struct'],true);
            $params['post_place']   = $others['floor'];
            $params['output_manager']= 0;

            $a = OrderOfficeSupply::model()->insert($params);

            foreach($others['stationery'] as $key => $stationery){
                $supplies_params = array();
                $supplies_params['order_id'] = $value['doc_id'];
                $supplies_params['supply_id'] = 0;
                $supplies_params['supply_name'] = $stationery['name'];
                $supplies_params['detail_info'] = isset($stationery['detail']) ? $stationery['detail'] : '';
                $supplies_params['apply_number'] = $stationery['num'];
                $supplies_params['status'] = 1;
                $supplies_params['update_time'] = $value['create_time'];

                //var_dump($supplies_params);die();
                $b = OrderOfficeDetail::model()->insert($supplies_params);
                //var_dump($b);die();
            }
        }
    }

}
