<?php
namespace Admin\Modules\Hr\Leave;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\OptionWorkflow;
/**
 * 获取审批流程
 * @author hongzhou@meilishuo.com
 * @since 2015-11-23
 */

class AjaxApproveLeaveInfo extends BaseModule {

    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
        $this->_init();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $p['task_id'] = $this->params['task_id'];
        $p['speed_user_id']=$this->user['id'];
        //获取审批过的流程
        $approve_info = OptionWorkflow::getInstance()->getProcessList($p);
        $nextApprove  = OptionWorkflow::getInstance()->getTaskInfoById($p);
        if(!$approve_info|| !$nextApprove){
            $return = Response::gen_error(30001,'在工作流中没有这个流程id');
            return $this->app->response->setBody($return);
        }
        $nextUser     = $nextApprove['current_user_name'][0];

        $result = array();
        foreach($approve_info['process_node_info'] as $key=>&$value){
            if($value['type'] == 'persion'){   //申请请假的人
                $result[$key]['type'] = $value['type'];
                $progress = array_pop($value['progress_info']);
                $result[$key]['name'] = '申请人';
                $result[$key]['user_name']        = $progress['user_name'];
                $result[$key]['status']           = $progress['progress']['status'];
                $result[$key]['action_type']      = $progress['progress']['action_type'];
                $result[$key]['create_time']      = $progress['progress']['create_time'];
                $result[$key]['progress_content'] = '提交申请';
                $result[$key]['timeline_status']  = 'submit';
                $result[$key]['approve_user_name'][] = array('user_id'=>$progress['user_id'],'user_name'=>$progress['user_name']);
            }else if($value['type'] == 'group'){
                $result[$key]['type'] = $value['type'];
                $result[$key]['name'] = $value['name'] == '逐级审批，直到svp' ? '逐级' : $value['name'];
                $result[$key]['approve_user_name'] = $value['progress_info'];
                $progress = reset($value['progress_info']);
                $res = '';
                foreach($value['progress_info'] as $info){
                     $res .= $info['user_name'].' ';
                }

                $result[$key]['user_name'] = $res;
                //$result[$key]['user_name']  = $progress['user_name'];
                if($nextUser == $progress['user_name']){ //判断是否是下一个审批人
                    $result[$key]['timeline_status'] = 'wait';
                }else{
                    $result[$key]['timeline_status'] = 'inwait';
                }

                if(!empty($progress['progress'])){
                    $result[$key]['action_type']      = $progress['progress']['action_type'];
                    $result[$key]['create_time']      = $progress['progress']['create_time'];
                    $result[$key]['progress_content'] = $progress['progress']['progress_content'];

                    if($progress['progress']['status'] == 1){
                        $result[$key]['timeline_status'] = 'submit';  //新建
                    }else if($progress['progress']['status'] == 2 || $progress['progress']['status'] == 3){
                        $result[$key]['timeline_status'] = 'wait';  //待接收和处理中
                    }else if($progress['progress']['status'] == 6){
                        $result[$key]['timeline_status'] = 'repeal';
                        $result[$key]['name'] = '申请人';
                        break;
                    }

                    if($progress['progress']['action_type'] == 1){
                        $result[$key]['timeline_status'] = 'pass-o';  //当前审批人通过
                        if(isset($value['is_end'])){
                            $result[$key]['timeline_status'] = 'pass';
                        }
                    }elseif($progress['progress']['action_type'] == 2){
                        $result[$key]['timeline_status'] = 'reject';  //当前审批人驳回
                        break;
                    }else{
                        $result[$key]['timeline_status'] = 'wait';  //待接收和处理中
                    }
                }

            }else if($value['type'] == 'cascading'){   //逐级审批
                foreach($value['progress_info'] as $k=>$val){
                    $result[$k]['name'] = $value['name'] == '逐级审批，直到svp' ? '逐级' : $value['name'];
                    $result[$k]['approve_user_name'][] = array('user_id'=>$val['user_id'],'user_name'=>$val['user_name']);
                    $result[$k]['type'] = $value['type'];
                    $result[$k]['user_name'] = $val['user_name'];
                    if($nextUser == $val['user_name']){  //判断是否是下一个审批人
                        $result[$k]['timeline_status'] = 'wait';
                    }else{
                        $result[$k]['timeline_status'] = 'inwait';
                    }

                    if(isset($val['progress'])){
                        $result[$k]['create_time']       = $val['progress']['create_time'];
                        $result[$k]['progress_content']  = $val['progress']['progress_content'];
                        $result[$k]['action_type']       = $val['progress']['action_type'];
                        if($val['progress']['status'] == 1){
                            $result[$k]['timeline_status'] = 'submit';  //新建
                        }else if($val['progress']['status'] == 2 || $val['progress']['status'] == 3){
                            $result[$k]['timeline_status'] = 'wait';  //待接收和处理中
                        }else if($val['progress']['status'] == 6){
                            $result[$k]['timeline_status'] = 'repeal';
                            $result[$k]['name'] = '申请人';
                            break;
                        }

                        if($val['progress']['action_type'] == 1){
                            $result[$k]['timeline_status'] = 'pass-o';  //当前审批人通过
                            if(isset($val['is_end'])){
                                $result[$k]['timeline_status'] = 'pass';  //最后一个审批人通过
                            }
                        }elseif($val['progress']['action_type'] == 2){
                            $result[$k]['timeline_status'] = 'reject';  //当前审批人驳回
                            break;
                        }else{
                            $result[$k]['timeline_status'] = 'wait';  //待接收和处理中
                        }

                    }

                }
            }
        }

        $result = array_reverse($result,true);


        if($result === FALSE) {
            $return = Response::gen_error(10004);
        }else if(isset($result['error_code']) && !empty($result['error_code'])) {
            $return = Response::gen_error($result['error_code']);
        }else {
            $return = Response::gen_success($result);

        }

    	$this->app->response->setBody($return);
	}

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
            'task_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),

        );

        $this->params = $this->query()->safe();
    }

}
