<?php
namespace Admin\Modules\Hr\Attendance;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\Attendance;
use Admin\Package\Attendance\WorkingHours;
/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
class AttendanceHome extends BaseModule {
     protected $errors = NULL;
     private $params = NULL;
     protected $checkUserPermission = TRUE;
     protected $approval_half = array(
        0=>'正常上班',1=>'上午请假',2=>'下午请假',3=>'全天请假', 8 => '节假日','9' =>'周六日'
    );
    //请假类型
    protected $approval_type = array(
       0=>'-' ,1=>'事假',2=>'年假',3=>'病假',4=>'带薪病假',5=>'婚假',6=>'丧假',7=>'产假',8=>'陪产假',9=>'产检假',10=>'流产假'
    );
    //请假状态
    protected $approval_status =  array(1=>'新建',2=>'待接收',3=>'处理中',4=>'完成',5=>'驳回',6=>'失效');
    protected $abnormal_state =  array(0=>'无异常',1=>'上午请假下午未打卡',2=>'下午请假但上午未打卡',3=>'全天未打卡');
    protected $page_size = 20;
     public function run() {
         $this->_init();
           //分页控制
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['page'] = 1;
            }
         
        }
       
		//参数校验
        if(empty($this->params['search'])&&empty($this->params['attendance_start_date'])&&empty($this->params['attendance_end_date'])){
            $return['count'] = 0;
            $return['page'] = 1;
            $return['data'] = array();
            return $this->app->response->setBody($return);
        }

		//获取 ,查询需要考勤的用户的信息
         $query_params =  array();
        //查询考勤时间的信息
        $attendance_params = array();
        //分页控制

         if ($this->params['page'] <= 0) {
              $this->params['page']= 1;
         }
         $query_params['offset']= ($this->params['page']-1) *  $this->page_size;
         $query_params['limit'] = $this->page_size;

        if(!empty($this->params['search'])){
           $query_params['name_cn'] = $this->params['search'];
        }
        if(!empty($this->params['attendance_start_date'])){
           $attendance_params['attendance_start_date'] = date('Y-m-d',strtotime($this->params['attendance_start_date']));
        }
         if(!empty($this->params['attendance_end_date'])){
           $attendance_params['attendance_end_date'] = date('Y-m-d',strtotime($this->params['attendance_end_date']));
        }
        if(!empty($this->params['staff_id'])){
           $query_params['staff_id'] = $this->params['staff_id'];
        }
        //查询总的页数
        $count_params = $query_params;
        $count_params['count'] = 1;
        
        $temp_count = WorkingHours::getInstance()->getStaffWorkingHours($count_params);
        
        //获取所有的考勤规则
        $all_working =$this -> getAllWorking();
        //获取邮箱的列表
        $work_staff_list = array();
        if($temp_count >0){
            //获取数据
            $work_staff_list =  WorkingHours::getInstance()->getStaffWorkingHours($query_params);
       
            if(!empty($work_staff_list)){
                foreach($work_staff_list as $key => &$work_staff_value){
                    $attendance_params['user_id'] = $work_staff_value['user_id'];
                    $attendance_params['all'] = 1;
                    $work_staff_value['work_rule'] = $this->filterValue($work_staff_value['work_id'] ,$all_working);
                    $attendance_list = Attendance::getInstance()->getAttendanceList($attendance_params);
                    foreach($attendance_list as &$attendance_value){
                        
                        $attendance_value['approval_half'] = $this->filterValue($attendance_value['approval_half'],$this->approval_half);
                        $attendance_value['approval_am_type'] = $this->filterValue($attendance_value['approval_am_type'],$this->approval_type);
                        $attendance_value['approval_am_status'] = $this->filterValue($attendance_value['approval_am_status'],$this->approval_status);
                        $attendance_value['approval_pm_type'] = $this->filterValue($attendance_value['approval_pm_type'],$this->approval_type);
                        $attendance_value['approval_pm_status'] = $this->filterValue($attendance_value['approval_pm_status'],$this->approval_status);
                        $attendance_value['abnormal_state'] = $this->filterValue($attendance_value['abnormal_state'],$this->abnormal_state);
                        $attendance_value['late_time'] = number_format($attendance_value['late_time']/60,2);
                        $attendance_value['early_time'] = number_format($attendance_value['early_time']/60,2);
                    }
                
                    unset($attendance_value);
                    $work_staff_value['list'] = $attendance_list;
                }
                unset($work_staff_value);
            }
            $return['data'] = $work_staff_list;
        }else{
          $temp_count = 0;
        }
        $return['search_params'] = $this->params;
        $return['count'] = ceil(intval($temp_count) / $this->page_size);
        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);
        
    }

      /**
     * 获取所有的考勤规则
     */
    public function getAllWorking(){
        $params = array('all' => 1,'status' => 1);
        $all_working = WorkingHours::getInstance()->getWorkingHours($params);
        $news_working = array();
        foreach($all_working as $key => $value){
            $news_working[$key] = $value['morning_start'].'-'. $value['afternoon_end'];
        }
        return $news_working;
    }
    
    
    /**
     * 根据key 获取值
     * @param       $key
     * @param array $data
     *
     * @return string
     */
    public function filterValue($key,$data = array()){
        if(empty($key)  || empty($data)) return '';
        if(isset($data[$key])) return $data[$key];
        return '';

    }
    
	private function _init() {
		
		$this->rules = array(
			'search' => array(// 邮箱 姓名 拼音
				'type' => 'string',
			),
			'attendance_start_date' => array(
				'type' => 'string',
			),
			'attendance_end_date' => array(
				'type' => 'string',
			),
			'all'  => array(
				'type'    => 'integer',
				'default' => 0,
			),
            'staff_id' =>  array(
				'type'    => 'string',
				
			),
            'page' =>  array(
                'type'    => 'integer',
                'default' => 1,
			),
            

		);

		$this->params = $this->request()->safe();
		$this->errors = $this->request()->getErrors();
	}

}