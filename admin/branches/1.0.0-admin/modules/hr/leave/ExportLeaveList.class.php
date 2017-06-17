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
 * Created by guojiezhu@meilishuo.com  hongzhou@meilishuo.com
 * User: MLS  ExportLeaveList
 * Date: 15/11/13
 * Time: 下午12:18
 */
class ExportLeaveList extends BaseModule{

    protected $errors = null;
    private $params = null;
    public $page_size = 50;
    protected $config = array (
        'filename' => '请假报表',
        'config'   => array (
            'A' => array ('width' => 15, 'title' => '员工编号', 'field' => 'staff_id'),
            'B' => array ('width' => 15, 'title' => '姓名', 'field' => 'name_cn'),
            'C' => array ('width' => 15, 'title' => '部门', 'field' => 'depart_name'),
            'D' => array ('width' => 15, 'title' => '请假单创建时间', 'field' => 'create_time'),
            'E' => array ('width' => 15, 'title' => '假单类型', 'field' => 'absence_name', ),
            'F' => array ('width' => 15, 'title' => '假单工作日天数', 'field' => 'meal_day'),
            'G' => array ('width' => 80, 'title' => '请假事由', 'field' => 'reason'),
            'H' => array ('width' => 15, 'title' => '当前审批人', 'field' => 'approve'),
            'I' => array ('width' => 15, 'title' => '审批状态', 'field' => 'approve_status'),
            'J' => array ('width' => 15, 'title' => '请假开始时间', 'field' => 'start_date'),
            'K' => array ('width' => 15, 'title' => '请假开始时段', 'field' => 'start_half'),
            'L' => array ('width' => 15, 'title' => '请假结束时间', 'field' => 'end_date'),
            'M' => array ('width' => 15, 'title' => '请假结束时段', 'field' => 'end_half'),


        )
    );
    
    public function run()
    {
        $this->_init();

        //参数校验
        $leave_list = array();
        if (empty($this->params['create_time']) && empty($this->params['end_time'])) {
            echo "请选择开始和结束时间";
            exit;
        }
        //时间控制就算了吧 哥已经做了多次请求了
        if( abs((strtotime($this->params['end_time']) - strtotime($this->params['create_time']))) >365*86400 ){
            echo "导出天数超过365天";
            exit;
        }

        //获取 ,查询需要考勤的用户的信息
        $query_params = array();
        //查询请假的时间
        $query_params['start_time'] = $this->params['create_time'];
        $query_params['end_time'] = $this->params['end_time'];
        $query_params['status'] = $this->params['status'];
        $query_params['speed_user_id'] = $this->user['id'];
        $query_params['tasktype_id'] =  Config::getInstance()->getValue(array('path'=>'/hr/leave/sub_type_id'));
        $task_array = OptionWorkflow::getInstance()->getTaskIdByDate($query_params);

        $results=$this->getData($task_array);

//   die();
        set_time_limit (0);
        $this->config['filename'] = '请假报表'. $query_params['start_time'].'-'.
        $query_params['end_time'];
//        echo '<pre>';
//        var_dump($results);die();
        $excelObject =  new ExportExcel($this->config,$results);
        $excelObject -> output();
        exit;

    }
    /**
     * 拼装数据
     */
    public function getName($result = array() ,$info=array()){
        $temp=$user_info =array();
        $t=NULL;
        $user_id =  ArrayUtilities::my_array_column($result,'user_id');
        $user_id['user_id'] = array_unique($user_id);
        $user_id['status'] = explode(',',$this->params['user_status']);//控制拿出是否在职类型

        $user_info = UserInfo::getInstance()->getUserInfo($user_id);  //获取用户姓名

        $depart_id = ArrayUtilities::my_array_column($user_info,'depart_id');
        $depart_id['depart_id'] = array_unique($depart_id);
        $depart_id['status'] = array(0,1);
        $depart_info = Department::getInstance()->getDepart($depart_id);  //获取所在部门
        $depart_info  = $this->valueToKey($depart_info,'depart_id');


        foreach($result as &$value){

             if(isset($user_info[$value['user_id']]['user_id']) && $value['user_id'] ==$user_info[$value['user_id']]['user_id']){
                $value['name_cn'] = isset($user_info[$value['user_id']]['name_cn'])?$user_info[$value['user_id']]['name_cn']:'';
                $value['depart_name'] = isset($depart_info[$user_info[$value['user_id']]['depart_id']]['depart_name']) ? $depart_info[$user_info[$value['user_id']]['depart_id']]['depart_name']:'';
                $value['depart_id']   = isset($user_info[$value['user_id']]['depart_id'])?$user_info[$value['user_id']]['depart_id']:'';
                $value['staff_id']   = isset($user_info[$value['user_id']]['staff_id'])?$user_info[$value['user_id']]['staff_id']:'';
                   // 组装审批人

                    if(isset($value['current_user_id'])&&is_array($value['current_user_id'])){


                        foreach($value['current_user_id'] as $k=>$v){
//                            echo '<pre>';
//                            var_dump($v);
                            $t .= isset($info[$v]['name_cn'])?($info[$v]['name_cn'].' '):'';

//                            echo '<pre>';
//                            var_dump($t);

                        }
                        $value['current_user_id'] = $t;
//                        echo '<pre>';
//                        var_dump($t);die();
                    }else{
                        $value['current_user_id'] =isset($info[$value['current_user_id']]['name_cn'])?$info[$value['current_user_id']]['name_cn']:'';

                    }
                    // 组装状态
                    if(isset($value['status'])){
                        switch($value['status']){
                            case 1:
                                $value['status']='新建';
                                break;
                            case 2:
                                $value['status']='待接收';
                                break;
                            case 3:
                                $value['status']='处理中';
                                break;
                            case 4:
                                $value['status']='审批完成';
                                break;
                            case 5:
                                $value['status']='驳回';
                                break;
                            case 6:
                                $value['status']='失效';
                                break;

                        }
                    }
             }
            $temp[] =$value;
        }
//        var_dump($temp);die();
        return $temp;
    }

    /**
     * 返回数组中指定的一列值为索引的数组
     */
    public  function  valueToKey($array=array(),$col){

        $results=array();
        if(!is_array($array)) return $results;

        foreach($array as &$value){
            if(isset($value[$col])){
                $results[$value[$col]] =$value;
            }
        }
        return $results;
    }
    /**
     * 获取数据
     */
    public  function  getData($task_array=array()){

        $results=array();
        $task_id_array= ArrayUtilities::my_array_column($task_array,'task_id');
        $count_id_array =ceil(count($task_id_array)/$this->page_size);
        for( $i =0;$i<$count_id_array;$i++){
            $temp_task_id_array =  array_slice($task_id_array,$i*$this->page_size,$this->page_size);
            if(empty($temp_task_id_array) ){
                continue;
            }


                $leave_params = array(
                    'task_id' => is_array($temp_task_id_array)?implode(',', $temp_task_id_array):$temp_task_id_array,
                    'all' =>1,
                    'sort' => 'DESC',
                    'type' => 1,
                );

                if(!empty($this->params['absence_type'])){//某种类型
                    $leave_params['absence_type']=$this->params['absence_type'];
                }
                if(!empty($this->params['user_id'])){//某人信息
                    $leave_params['user_id']=$this->params['user_id'];
                }
                $leave_list = LeaveList::getInstance()->getLeaveList($leave_params);

                $leave_list =$this->valueToKey($leave_list,'task_id');
                $task_array =$this->valueToKey($task_array,'task_id');

                foreach($leave_list as $k =>$v){
                    $leave_list[$k]['current_user_id'] = isset($task_array[$k]['current_user_id'])
                        ? $task_array[$k]['current_user_id']:'';
                    $leave_list[$k]['status'] = isset($task_array[$k]['status'])
                        ? $task_array[$k]['status']:'';
                }
//            echo '<pre>';
//            var_dump($leave_list);die();

                $leave_list =array_values($leave_list);

                $sum      =  count($leave_list);

                //总数 分批拿数据  50 最后合并数组
                if(!empty($leave_list)){
                    //获取请假人信息
                    $leave_list = $this->multiRequestResult($leave_list,$sum,50);
                }
                $temp = $leave_list;
                //可有可无 部门处理
                if(!empty($this->params['depart_id'])){
                    $temp =array();
                    foreach($leave_list as $k=>$v ){
                        if(isset($v['depart_id'])&& $v['depart_id']==$this->params['depart_id']){
                            $temp[$k] =$v;
                        }
                    }
                }

                //员工编号、姓名、部门、请假时段（与员工请假界面相符，包含上下午）、请假类型、扣除餐补天数 审批人 审批状态
                $leave_list =array();

                foreach($temp as $k=>$v ){
                    if(isset($v['staff_id'])&& !empty($v['staff_id'])){
                        $leave_list[$k]['staff_id']=$v['staff_id'];
                        $leave_list[$k]['name_cn']=isset($v['name_cn'])?$v['name_cn']:'';
                        $leave_list[$k]['depart_name']=isset($v['depart_name'])?$v['depart_name']:'';
                        $leave_list[$k]['absence_name']=isset($v['absence_name'])?$v['absence_name']:'';
                        $leave_list[$k]['meal_day'] =isset($v['length'])?$v['length']:'';
                        $leave_list[$k]['reason'] =isset($v['memo'])?$v['memo']:'';
                        $leave_list[$k]['approve']=isset($v['approve'])?$v['approve']:'';
                        $leave_list[$k]['approve_status'] =isset($v['status'])?$v['status']:'';
                        $leave_list[$k]['approve']=isset($v['current_user_id'])?$v['current_user_id']:'';
                        $leave_list[$k]['start_date']=isset($v['start_date'])?$v['start_date']:'';
                        $leave_list[$k]['start_half']=isset($v['start_half'])?$v['start_half']:'';
                        $leave_list[$k]['end_date']=isset($v['end_date'])?$v['end_date']:'';
                        $leave_list[$k]['end_half']=isset($v['end_half'])?$v['end_half']:'';
                        $leave_list[$k]['create_time']=isset($v['create_time'])?$v['create_time']:'';
                    }

                }
                $results = array_merge($results,$leave_list);

        }

        //出去合并后获得的多余选项
        foreach($results as &$v ){
            if(isset($v['staff_id'])&& empty($v['staff_id'])){
                unset($results[$v] );
            }
        }
        return $results;
    }
    public function test_odd(&$var)
    {
        return($var & 1);
    }
    /**
     * 防止数据丢失 多次请求
     */
    public  function  multiRequestResult($array=array(),$sum,$limit){

        $results=array();
        if(!is_array($array)) return $results;
        $info = UserInfo::getInstance()->getUserInfo(
            array(
                'all'=>1,
                'status'=>'1,2,3'
            )
        );  //获取用户姓名

        $count =ceil($sum/$limit);
        for( $i =0;$i<$count;$i++){
           //数组的截取
           $temp =  array_slice($array,$i*$limit,$limit);
           $temp = $this->getName($temp,$info);
           //数组的合并
           $results = array_merge($results,$temp);
        }
        return $results;
    }
    private function _init()
    {

        $this->rules = array(
           
            'create_time' => array(
                'type' => 'string',
                'required'	=> true,
            ),
            'end_time' => array(
                'type' => 'string',
                'required'	=> true,
            ),
            'depart_id' => array(
                'type' => 'integer',
            ),
            'absence_type' => array(
                'type' => 'integer',
            ),
            'user_status' => array(
                 'type' => 'string',
                 'default'=>'1,2,3'
            ),
            'user_id' => array(
                'type' => 'integer',
            ),
            'status' => array( //1,2未审批  审批完
                'type' => 'multiId',
                'default'=>array(1,2,3,4,5,6)

             ),
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }



}