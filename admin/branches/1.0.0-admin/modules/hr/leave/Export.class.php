<?php
namespace Admin\Modules\Hr\Leave;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Hr_leave\LeaveList;
use Admin\Modules\Common\ExportExcel;
use Admin\Package\Core\Config;
use Admin\Package\Workflow\OptionWorkflow;
use Admin\Package\Account\UserInfo;
use Admin\Package\Department\Department;
use  Libs\Util\ArrayUtilities;
/**
 * Created by  hongzhou@meilishuo.com
 * User: MLS  Export
 * 导出带薪病假 事假 病假  年假使用情况
 * Date: 15/11/13
 * Time: 下午12:18
 */
class Export extends BaseModule{

    public $start_date;
    public static $VIEW_SWITCH_JSON = TRUE;
    //配置信息
    protected $config = array (
        'filename' => '请假报表',
        'config'   => array (
            'A' => array ('width' => 15, 'title' => '员工名字', 'field' => 'name_cn'),
            'B' => array ('width' => 15, 'title' => '员工编号', 'field' => 'staff_id' ),
            'C' => array ('width' => 15, 'title' => '部门名称', 'field' => 'depart_name' ),

            'D' => array ('width' => 15, 'title' => '事假剩余', 'field' => 's_usable'),
            'E'=> array ('width' => 15, 'title' => '事假流程中', 'field' => 's_going'),
            'F' => array ('width' => 15, 'title' => '事假全部', 'field' => 's_all'),
            'G' => array ('width' => 15, 'title' => '事假使用', 'field' => 's_used'),

            'H' => array ('width' => 15, 'title' => '病假剩余', 'field' => 'b_usable'),
            'I'=> array ('width' => 15, 'title' => '病假流程中', 'field' => 'b_going'),
            'J' => array ('width' => 15, 'title' => '病假全部', 'field' => 'b_all'),
            'K' => array ('width' => 15, 'title' => '病假使用', 'field' => 'b_used'),

            'L' => array ('width' => 15, 'title' => '带薪病假剩余', 'field' => 'd_usable'),
            'M'=> array ('width' => 15, 'title' => '带薪病假流程中', 'field' => 'd_going'),
            'N' => array ('width' => 15, 'title' => '带薪病假全部', 'field' => 'd_all'),
            'O' => array ('width' => 15, 'title' => '带薪病假使用', 'field' => 'd_used'),


            'P'=> array ('width' => 15, 'title' =>  '年假流程中', 'field' => 'n_going'),
            'Q' => array ('width' => 15, 'title' => '年假全部', 'field' => 'n_all'),
            'R' => array ('width' => 15, 'title' => '年假使用', 'field' => 'n_used'),
            'S' => array ('width' => 15, 'title' => '今年年假剩余', 'field' => 'n_usable'),
            'T' => array ('width' => 15, 'title' => '去年年假剩余', 'field' => 'n_last' ),

            'U' => array ('width' => 15, 'title' => '入职时间', 'field' => 'hire_time' ),
            'V' => array ('width' => 15, 'title' => '毕业时间', 'field' => 'graduation_time' ),
            'W' => array ('width' => 15, 'title' => '员工id', 'field' => 'user_id' ),

        )
    );
    public function run() {
        $this->_init();

        $all_user=array();
        $this->start_date = (date('Y',time())-1).'-01'.'-01';
        $depart = "http://atom.speed.meilishuo.com/department/depart_info_list?all=1&status=".'0,1';

        $depart = $this->curl_data($depart);

        $d =$s =null;
        if(!empty($this->params['depart_id'])){
            $d ='&depart_id='.$this->params['depart_id'];
        }
        if(!empty($this->params['user_id'])){
            $s = '&user_id='.$this->params['user_id'];
        }
        if(!empty($this->params['status'])&&is_array($this->params['status'])){
            $this->params['status'] = implode(',',$this->params['status']);
        }
        $data = 'http://atom.speed.meilishuo.com/account/get_user_info?all=1'.$d.$s.'&status='.$this->params['status'] ;
        $all_user = $user_array = $this->curl_data($data);



        $end = $temp =$result=array();
        foreach($user_array as $key =>$value){

            $multiClient = self::getMultiClient();
            $multiClient->call('atom', 'hr_leave/leave_annual_admin', array('user_id'=>$value['user_id'],'start_date'=>$this->start_date),'annual');
            $multiClient->call('atom', 'hr_leave/leave_paid_sick',array('user_id'=>$value['user_id']),'paid');
            $multiClient->call('atom', 'hr_leave/leave_sick',array('user_id'=>$value['user_id']), 'sick');
            $multiClient->call('atom', 'hr_leave/leave_absence',array('user_id'=>$value['user_id']),'absence');
            $multiClient->callData();
            $annual = $multiClient->annual;
            $result = $this->data_merge($result,$annual,'n_');

            $paid = $multiClient->paid;
            $result = $this->data_merge($result,$paid,'d_');

            $sick = $multiClient->sick;
            $result = $this->data_merge($result,$sick,'b_');

            $absence = $multiClient->absence;
            $result = $this->data_merge($result,$absence,'s_');
            $result['name_cn'] =isset($all_user[$value['user_id']]['name_cn'])?$all_user[$value['user_id']]['name_cn']:'';
            $result['user_id'] =isset($all_user[$value['user_id']]['user_id'])?$all_user[$value['user_id']]['user_id']:'';
            $result['depart_name'] =isset($depart[$value['depart_id']]['depart_name'])?$depart[$value['depart_id']]['depart_name']:'';
            $result['staff_id'] =isset($all_user[$value['user_id']]['staff_id'])?$all_user[$value['user_id']]['staff_id']:'';
            $result['hire_time'] =isset($all_user[$value['user_id']]['hire_time'])?$all_user[$value['user_id']]['hire_time']:'';
            $result['graduation_time'] =isset($all_user[$value['user_id']]['graduation_time'])?$all_user[$value['user_id']]['graduation_time']:'';
            $end[]= $result;

        }
//        exit;
        $this->config['filename'] = '请假使用情况报表';
        $excelObject =  new ExportExcel($this->config,$end);
        $excelObject -> output();
        exit;
    }

    protected function _init(){
        $this->rules = array(
            'user_id' => array(
                'type'=>'integer',
            ),
            'depart_id' => array(
                'type'=>'integer',
            ),
            'status' => array(
                'type' => 'multiId',
                'default'=>array(1,2,3)
            ),
        );

        $this->params = $this->query()->safe();
    }
    public function data_merge($result =array(),$params  = array(),$flag ){
        $params = $this->parseApiData($params);
        if($params){
            $t =null;
            if($flag=='n_'){
                $t= isset($params['last_year_leave']['usable'])?$params['last_year_leave']['usable']:0;
            }

            $temp= array(
                $flag.'all' =>isset($params['all'])?$params['all']+$t:'',
                $flag.'used'=>isset($params['used'])?$params['used']:0,
                $flag.'going' =>isset($params['going'])?$params['going']:0,
                $flag.'usable' =>isset($params['usable'])?$params['usable']:0,
            );
            if($t===0||!empty($t)){
                $temp['n_last'] = $t;
                $temp['n_usable'] = isset($params['all'])?$params['all']:0;
            }

        }else{
            $temp= array(
                $flag.'all' =>0,
                $flag.'used' =>0,
                $flag.'usable' =>0,
                $flag.'going' =>0,
            );

        }
        return $result = array_merge($result,$temp);

    }
    /**
     * curl post 数据
     * @param       $url
     * @param array $params
     *
     * @return array|mixed
     */
    public function curl_data($url, $params = array(),$header = array(),$type='post'){
        $ret = array();
        $ch = curl_init ();
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        //post请求
        if(!empty($params)){
            $params = http_build_query($params);
            if($type=='get'){
                curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
            }else{
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }else{
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        }

        ob_start();
        curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        if(!empty($result)){
            $ret = json_decode($result,1);
        }
        if(isset($ret['data'])){
            return $ret['data'];
        }
    }


}