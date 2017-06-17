<?php
namespace Admin\Modules\Hr\Attendance;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\WorkingHours;
/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
class UpdateAddUser extends BaseModule {
     protected $errors = NULL;
     private $params = NULL;
     //protected $checkUserPermission = TRUE;

     public function run() {

         $this->_init();
         //新增还是 更新
         $work_count_params['all'] = 1;
         $work_hours_list = WorkingHours::getInstance()->getWorkingHours($work_count_params);
         $return['attendance_group'] = $work_hours_list;
         
         if ($this->params['id'] <= 0) {
              //新增
             
         }else{
             //更新
             $staff_info = WorkingHours::getInstance()->getStaffWorkingHours( array( 'id' => $this->params['id']));
             $return['staff_info'] = current($staff_info);

         }

        $return = Response::gen_success($return);
        return $this->app->response->setBody($return);
      
        
    }

	private function _init() {
		$this->rules = array(
            'id' =>  array(
                'type'    => 'integer',
                'default' => 0,
			)
		);
		$this->params = $this->request()->safe();
		$this->errors = $this->request()->getErrors();
	}

}