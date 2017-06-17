<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\Meeting;
use Admin\Package\Company\Office;
use Admin\Package\Meeting\RoomService;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * 会议室修改
 * Class Home
 *
 * @package Admin\Modules\Meeting
 */
class AjaxMeetingUpsert extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
//    protected $checkUserPermission = TRUE;
	public static $VIEW_SWITCH_JSON = TRUE;
	public function run() {
	    $this->_init();
		//获取office的数据
		$query_office = array(
			'office_id' => $this->params['office_id']
		);
		$office = Office::getInstance()->officeGet($query_office);
		$update_query = array();
		if(!empty($office)){
			$update_query = array(
				'city_id' => $office['city_id'],
				'company_id' => $office['company_id'],
				'office_id' => $office['office_id'],
			);
		}
		//获取当前服务信息。保存到room_service_rule

		if(!empty($this->params['room_sn'])){
			$update_query['room_sn'] = $this->params['room_sn'];
		}
		if(!empty($this->params['room_name'])){
			$update_query['room_name'] = $this->params['room_name'];
		}
		if(!empty($this->params['room_position'])){
			$update_query['room_position'] = $this->params['room_position'];
		}
		if(!empty($this->params['room_capacity'])){
			$update_query['room_capacity'] = $this->params['room_capacity'];
		}
		if(!empty($this->params['type'])){
			$update_query['type'] = $this->params['type'];
		}
		if(isset($this->params['status'])){
			$update_query['status'] = $this->params['status'];
		}
		if(isset($this->params['extension'])){
			$update_query['extension'] = $this->params['extension'];
		}
		if(isset($this->params['room_detail'])){
			$update_query['room_detail'] = $this->params['room_detail'];
		}
		//更新
		if(!empty($this->params['room_id'])){
			$update_query['room_id'] = $this->params['room_id'];
			$return = Meeting::getInstance()->meetingRoomUpdate($update_query);
			if($return >=0 ){
				//记录日志
				$log_info = array('user_id'=>$this->user['id'],
								  'handle_id'=> $this->params['room_id'],
								  'operation_type'=>  'update',
								  'after_data'=>  json_encode($update_query),
								  'handle_type'=> 7);

				Log::getInstance()->createLogs($log_info);
				$return = Response::gen_success('修改成功');
			}else{
				$return = Response::gen_error(50001,'更新失败');
			}
		}else{ //charu
			$return = Meeting::getInstance()->meetingRoomCreate($update_query);
			if($return >=0 ){
				$log_info = array('user_id'=>$this->user['id'],
								  'handle_id'=> $return,
								  'operation_type'=>  'add',
								  'after_data'=>  json_encode($update_query),
								  'handle_type'=> 7);

				Log::getInstance()->createLogs($log_info);
				$return = Response::gen_success('修改成功');
			}else{
				$return = Response::gen_error(50001,'更新失败');
			}
		}

        return $this->app->response->setBody($return);


	}


	private function _init() {

		$this->rules = array(
			'room_id'  => array(
				'type'    => 'integer',
				'default' => 0
			),
			'room_sn'  => array(
				'type'    => 'string',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'room_name'  => array(
				'type'    => 'string',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'room_position'  => array(
				'type'    => 'string',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),

			'room_capacity'  => array(
				'type'    => 'integer',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'extension'  => array(
				'type'    => 'string',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),

			'type'  => array(
				'type'    => 'integer',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'office_id'  => array(
				'type'    => 'integer',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'status'  => array(
				'type'    => 'integer',
				'required' => TRUE,
				'allowEmpty' => FALSE,
			),
			'room_detail'  => array(
				'type'    => 'string',
			),

			'room_service'  => array(
				'type'    => 'multiID',
			),

		);
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
	}
	
}