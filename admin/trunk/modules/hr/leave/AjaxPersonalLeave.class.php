<?php
namespace Admin\Modules\Hr\Leave;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\LeaveList;
use Admin\Package\Core\Config;
use Admin\Package\Account\UserInfo;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxPersonalLeave
 * Date: 15/11/25
 * Time: 下午12:18
 */
class AjaxPersonalLeave extends BaseModule {

    protected $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
        $this->_init();
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        $params = array(
            'path' => '/hr/leave/leave_instruction',
        );
        $result = Config::getInstance()->getChild($params);

        foreach($result as $key=>$value){
            $result[$key] = json_decode($value,true);
        }
        $params['user_id'] = $this->params['user_id'];
        $params['status'] = array(1,2,3);
        $user_info = UserInfo::getInstance()->getUserInfo($params);
        is_array($user_info)&&$user_info = array_pop($user_info);

        $params['absence_type'] = 1;
        $leave = LeaveList::getInstance()->getLeaveAbsence($params);

        $result['1'] = array_merge($result['1'],$leave);

        $params['absence_type'] = 3;
        $leave = LeaveList::getInstance()->getLeaveSick($params);

        $result['5'] = array_merge($result['5'],$leave);

        //带薪病假
        $params['absence_type'] = 4;
        $result['6']['usable']  = '无';
        $result['6']['fact']    = 1;
        $leave = LeaveList::getInstance()->getLeavePaidSick($params);
        if($user_info['flag'] == 3){
            $result['6'] = array_merge($result['6'],$leave);
        }

        //婚假
        $result['7']['usable'] = '按实际情况计算';
        $result['7']['fact'] = 1;

        //丧假
        $params['absence_type'] = 6;
        $leave = LeaveList::getInstance()->getLeaveFuneral($params);
        $result['8'] = array_merge($result['8'],$leave);
        $result['8']['fact'] = 1;

        //陪产假
        $result['10']['usable'] = '按实际情况计算';
        $result['10']['fact']   = 1;
        if($user_info['flag'] == 3 && $user_info['gender'] == 1){
            $params['absence_type'] = 8;
            $leave = LeaveList::getInstance()->getLeavePaternity($params);
            $leave['all'] = isset($leave['usable'])?$leave['usable']:'';
            $leave['going'] = 0;
            $leave['used'] = 0;
            $result['10'] = array_merge($result['10'],$leave);
        }

        //产检假
        $params['absence_type'] = 9;
        $leave = LeaveList::getInstance()->getLeaveDetection($params);
        $result['11'] = array_merge($result['11'],$leave);
        $result['11']['usable'] = '按实际情况计算';
        $result['11']['fact'] = 1;
        //产假
        $result['9']['usable'] = '按实际情况计算';
        $result['9']['fact'] = 1;
        //流产假
        $result['12']['usable'] = '按实际情况计算';
        $result['12']['fact'] = 1;

        //年假
        $result = $this->annual($user_info,$result,$params);
//var_dump($result);die();
        if($result === FALSE) {
            $return = Response::gen_error(10004);
        }else{
            $return = Response::gen_success($result);
        }
        $this->app->response->setBody($return);
    }
    //年假计算
    private function annual($user_info,$result,$params){
        if($user_info['graduation_time'] == '0000-00-00'){
            $time = time() - strtotime('2014-07-01');
        }else{
            $time = time() - strtotime($user_info['graduation_time']);
        }

        $this_year = 24*3600*365;
        $params['absence_type'] = 2;      //年假
        $leave = LeaveList::getInstance()->getLeaveAnnual($params);
        //判断去年的年假是否可以使用
        $annual_param = array(
            'path' => '/hr/leave/leave_annual',
        );

        $leave_annual = Config::getInstance()->getValue($annual_param);
        $leave_annual = date('Y').'-'.$leave_annual;

        $result['3']['usable'] = '无';
        $result['3']['fact']   = 1;
        $result['2']['usable'] = '无';
        $result['2']['fact']   = 1;
        $result['4']['usable'] = '无';
        $result['4']['fact']   = 1;
        $result['2']['last_year_annual'] = 0;
        if(!isset($leave['message'])){  //已经可以请年假
            if($time < $this_year){
                unset($result['2']['fact']);
                $result['2'] = array_merge($result['2'],$leave);  //毕业不足一年
//                if(time() < strtotime($leave_annual) && isset($leave['last_year_leave'])){ //去年的年假
//                    $result['last_year_annual'] = $this->last_year_annual($result[2],$leave);
//                }
            }else if($this_year <= $time && $time < 20*$this_year){
                unset($result['3']['fact']);
                $result['3'] = array_merge($result['3'],$leave);  //满一年小于二十年
//                if(time() < strtotime($leave_annual) && isset($leave['last_year_leave'])){  //去年的年假
//                    $result['last_year_annual'] = $this->last_year_annual($result[3],$leave);
//                }else{
//                    $result['3']['last_year_annual'] = 0;
//                }
            }else if($time >= 20*$this_year){
                unset($result['4']['fact']);
                $result['4'] = array_merge($result['4'],$leave);  //满二十年
//                if(time() < strtotime($leave_annual) && isset($leave['last_year_leave'])){
//                    $result['last_year_annual'] = $this->last_year_annual($result[4],$leave);
//                }else{
//                    $result['4']['last_year_annual'] = 0;
//                }
            }
        }

        return $result;
    }
    private function last_year_annual(&$result,$leave){
        $return['value'] = $result['value'];
        $return['limit'] = $result['limit'];
        $return['leave_instruction'] = $result['leave_instruction'];
        $return['salary_instruction'] = $result['salary_instruction'];
        $return = array_merge($return,$leave['last_year_leave']);
        unset($result['last_year_leave']);

        return $return;
    }
    /**
     * 参数初始化
     */
    protected function _init(){
        $data = $this->request->POST;
        $data_check = array(
            'user_id' => array(
                'required'	=> true,
                'type'	=> 'integer',
            ),

        );
//数据校验
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }
}