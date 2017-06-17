<?php

namespace Joint\Scripts\Approval;

use Joint\Package\Workflow\Workflow;
use Frame\Speed\Lib\Api;
/**
 * 单据处理
 * Class OrderOperateProcess
 * @package Joint\Scripts\Approval
 */
class OrderOperateProcess extends \Frame\Script{
    public function run(){
        //查询单据队列表信息

        $queryData = array(
            'status'=>0,//未发送
            'limit'=>100,
        );
        $records = Api::atom('approval/get_order_operate_queue',$queryData);
        if(!empty($records)){
            foreach($records as $r){
                //修改为处理中状态
                $p = array(
                    'id' => $r['id'],
                    'status' => 1,//处理中
                );
                Api::atom('approval/update_order_operate_queue',$p);

                //调用工作流处理任务
                $workflow = $this->_pushWorkflow($r);
                if($workflow === false){
                    //处理失败
                    $this->_updateQueue($r['id'],9);
                }else{
                    //回调
                    $res = $this->_callBack($r,$workflow);

                    if($res){
                        //处理成功
                        $this->_updateQueue($r['id'],10);
                    }
                }
            }
        }else{
            $date = date('Y-m-d H:i:s');
            $this->response->setBody("send_time:{$date} no data!\n");
            return false;
        }

        global $start;
        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    /**
     * 回调
     */
    private function _callBack($params,$workflow){
        $res = '';

        switch($params['operate_type']){
            case '1':   //提交
                break;
            case '2':   //审批
                if($workflow['status'] == 4){//审批完成
                    $status = 3;//审批完成
                }else if($workflow['status'] == 5){//审批驳回
                    $status = 4;//审批驳回
                }else{
                    $status = 2;//审批通过
                }
                $p_order = array(
                    'order_id'      => $params['order_id'],
                    'order_type'    => $params['order_type'],
                    'send_type'     => $status,
                    'status'        => $workflow['status'],
                    'handle_user_id'=> $params['handle_user_id'],
                );
                $res = Api::atom('approval/update_order_process', $p_order);
                break;
            case '3':   //撤销
                break;
            default:
                break;
        }

        return $res;
    }

    /**
     * 修改队列状态
     */
    private function _updateQueue($id,$status){
        $p = array(
            'id' => $id,
            'status' => $status,
        );
        Api::atom('approval/update_order_operate_queue',$p);
    }

    /**
     * 推送工作流
     */
    private function _pushWorkflow($params){
        $workflow = false;
        switch($params['operate_type']){
            case '1':   //提交
                break;
            case '2':   //审批
                $p = array(
                    'task_id'           => $params['task_id'],
                    'user_id'           => $params['handle_user_id'],
                    'action_type'       => $params['action_type'],
                    'progress_content'  => $params['progress_content'],
                    'operate'           => 'all',
                    'speed_user_id'     => '1',
                );
                //调用工作流处理任务
                $workflow = Workflow::getInstance()->processTask($p);
                break;
            case '3':   //撤销
                break;
            default:
                break;
        }

        return $workflow;
    }

}
