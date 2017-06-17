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
class AttendanceGroup extends BaseModule {
     protected $errors = NULL;
     private $params = NULL;
     //protected $checkUserPermission = TRUE;
     
     protected $page_size = 20;
     public function run() {
         $this->_init();
           //分页控制
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['page'] = 1;
            }
            $this->params['limit'] = $this->page_size;
        }

		//获取 ,查询需要考勤的用户的信息
         $query_params =  array();
        //查询考勤时间的信息
        $attendance_params = array();
        //分页控制

         if ($this->params['page'] <= 0) {
              $this->params['page']= 1;
         }
         $query_params['offset']= ($this->params['page'] -1 ) * $this->page_size;
         $query_params['limit'] = $this->page_size;

        if(!empty($this->params['search'])){
           $query_params['name'] = $this->params['search'];
        }

        //查询总的页数
        $count_params = $query_params;
        $count_params['count'] = 1;
        
        $temp_count = WorkingHours::getInstance()->getWorkingHours($count_params);
       
        if($temp_count >0){
            //获取数据
            $work_staff_list =  WorkingHours::getInstance()->getWorkingHours($query_params);
            $return['data'] = $work_staff_list;
        }else{
          $temp_count = 0;
            $return['data'] = array();
        }
        $return['search_params'] = $this->params;
        $return['count'] = ceil(intval($temp_count) / $this->page_size);
        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);
        
    }

	private function _init() {
		
		$this->rules = array(
			'search' => array(// 邮箱 姓名 拼音
				'type' => 'string',
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