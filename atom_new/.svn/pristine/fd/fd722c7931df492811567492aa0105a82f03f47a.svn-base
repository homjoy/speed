<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Migrate\Crab;
use Atom\Package\Approval\OrderLeave;
/**
 * 同步老SPEED 员工请假信息
 * Class SyncOldSpeedLeaveInfo
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedLeaveInfo extends \Frame\Script{
    public function run(){

        //同步请假信息.
        $this->getInfo(0,20000);

        return $this->response->setBody("同步结束.\n");
    }

    private function getInfo(){
        $data = Crab::model()->getLeaveInfo();

        $params = array();
        foreach($data as $value){
            $others = json_decode($value['doc_data_struct'],true);

            $params['order_id'] = $value['doc_id'];
            $params['absence_type'] = isset($others['absenceType']) ? $others['absenceType'] : 0;
            switch($others['absenceType']){
                case 0:            //事假
                    $params['absence_type'] = 1;
                    break;
                case 2:             //年假
                    $params['absence_type'] = 2;
                    break;
                case 8:             //病假
                    $params['absence_type'] = 3;
                    break;
                case 1:             //带薪病假
                    $params['absence_type'] = 4;
                    break;
                case 3:             //婚假
                    $params['absence_type'] = 5;
                    break;
                case 4:             //丧假
                    $params['absence_type'] = 6;
                    break;
                case 5:             //产假
                    $params['absence_type'] = 7;
                    break;
                case 6:             //陪产假
                    $params['absence_type'] = 8;
                    break;
                case 7:             //产检假
                    $params['absence_type'] = 9;
                    break;
                case 9:             //流产假
                    $params['absence_type'] = 10;
                    break;
            }
            $params['user_id'] = $value['user_id'];
            $params['start_date'] = $others['start'];
            $params['start_half'] = isset($others['half']['start']) ? $others['half']['start'] : '';
            $params['end_half'] = isset($others['half']['end']) ? $others['half']['end'] : '';
            $params['end_date'] = $others['end'];

            $params['length'] = floatval($others['length']);
            if(isset($others['annual_used'])){
                if(isset($others['annual_used']['thisYearUse']) && $others['annual_used']['thisYearUse'] != 'NaN'){
                    $params['this_year_used'] = $others['annual_used']['thisYearUse'];
                }else{
                    $params['this_year_used'] = 0;
                }
                if(isset($others['annual_used']['lastYearUse']) && $others['annual_used']['lastYearUse'] != 'NaN'){
                    $params['last_year_used'] = $others['annual_used']['lastYearUse'];
                }else{
                    $params['last_year_used'] = 0;
                }
            }else{
                $params['this_year_used'] = $params['length'];
                $params['last_year_used'] = 0;
            }

            $params['this_year_used'] = floatval($params['this_year_used']);
            $params['last_year_used'] = floatval($params['last_year_used']);
            $params['length'] = $params['this_year_used'] + $params['last_year_used'];
            $params['memo'] = isset($others['reason']) ? $others['reason'] : '';
            $params['update_time'] = $value['create_time'];
            $params['create_time'] = $value['create_time'];
            switch($value['doc_status']){
                case 0:            //审批中
                    $params['status'] = 3;
                    break;
                case 1:             //审批通过
                    $params['status'] = 4;
                    break;
                case 2:             //审批驳回
                    $params['status'] = 5;
                    break;
                case 5:             //撤销 --> 失效
                    $params['status'] = 6;
                    break;
                case 7:             //离职锁定 --> 失效
                    $params['status'] = 0;
                    break;
            }

           OrderLeave::model()->insert($params);
        }
    }
}
