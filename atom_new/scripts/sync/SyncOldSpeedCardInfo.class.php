<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Migrate\Crab;
use Atom\Package\Approval\OrderVisitingCard;
use Frame\Speed\Lib\Api;
/**
 * 同步老SPEED 员工名片信息
 * Class SyncOldSpeedCardInfo
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedCardInfo extends \Frame\Script{
    private $task_type_id = 5;
    private $create_node_id = 9;
    private $node_id = 11;

    public function run(){

        //同步名片信息.
        $this->syncInfo();

        return $this->response->setBody("同步结束.\n");
    }

    private function syncInfo(){
        $p = array(
            'doc_status' => array(0,1,2),
            'doc_type'  => 9,
        );
        $data = Crab::model()->getDocumentInfo($p);

        $params = array();
        foreach($data as $value){
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
            $p = array(
                'doc_id' => $value['doc_id'],
            );
            $process = Crab::model()->getDocumentProcessInfo($p);
            $progress = array();
            if(!empty($process)){
                $pro = array_pop($process);
                $process_status = 1;
                if($pro['doc_change_to_status'] == 2){
                    $process_status = 2;
                }elseif($pro['doc_change_to_status'] == 3){
                    $process_status = 1;
                }
                $progress = array(
                    'process_id'       => $this->node_id,
                    'action_type'      => $process_status,
                    'progress_content' => '',
                    'current_user_id'  => $pro['user_id'],
                    'status'           => $params['status'],
                );
            }

            $s_p = array(
                'task' => array(
                    'tasktype_id'     => $this->task_type_id,
                    'user_id'         => $value['user_id'],
                    'order_id'        => $value['doc_id'],
                    'task_name'       => '名片',
                    'task_content'    => '',
                    'current_user_id' => isset($pro['user_id'])?$pro['user_id']:$value['user_id'],
                    'status'          => $params['status'],
                    'current_node_id' => $this->node_id,
                ),
                'progress' => array(
                    array(
                        'process_id'       => 0,
                        'action_type'      => 0,
                        'progress_content' => '创建任务',
                        'current_user_id'  => $value['user_id'],
                        'status'           => 1,
                    ),
                    array(
                        'process_id'       => $this->create_node_id,
                        'action_type'      => 0,
                        'progress_content' => '系统初始化操作',
                        'current_user_id'  => 0,
                        'status'           => 3,
                    ),
                    array(
                        'process_id'       => $this->create_node_id,
                        'action_type'      => 0,
                        'progress_content' => '填写任务内容，开启任务审批流转',
                        'current_user_id'  => $value['user_id'],
                        'status'           => 3,
                    ),
                ),
            );
            if(!empty($progress)){
                $s_p['progress'][] = $progress;
            }
            $sync_params = array(
                'data' => array($s_p),
                'speed_user_id' => 1,
            );
            $res = $this->dataSync($sync_params);
            $task = array_pop($res);
//var_dump($res,'test');die();

            $params['order_id'] = $value['doc_id'];
            $params['task_id'] = $task[$value['doc_id']];
            $params['user_id'] = $value['user_id'];
            $params['update_time'] = $value['create_time'];
            $params['create_time'] = $value['create_time'];


            $others = json_decode($value['doc_data_struct'],true);
            $params['job']          = $others['role'];
            $params['english_job']  = $others['role_e'];
            $params['name']         = $others['name'];
            $params['english_name'] = $others['name_e'];
            $params['mobile']       = $others['phone'];
            $params['mail']         = $others['mail'];

            OrderVisitingCard::model()->insert($params);
        }
    }

    public static function dataSync($params = array()){
        $approve_leader = Api::workflow('task/data_sync', $params);
//var_dump($approve_leader);die();
        return $approve_leader;
    }
}
