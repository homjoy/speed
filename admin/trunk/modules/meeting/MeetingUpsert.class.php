<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\Meeting;
use Admin\Package\Company\Office;

/**
 * 会议室修改
 * Class Home
 *
 * @package Admin\Modules\Meeting
 */
class MeetingUpsert extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
//    protected $checkUserPermission = TRUE;
	public function run() {
	    $this->_init();
		$meeting_info = array('status' => 1);

		if($this->params['room_id'] >0) {
			if (empty($this->params['room_id'])) {
				$return = Response::getInstance()->gen_success(array());
				return $this->app->response->setBody($return);
			}
			$meeting_info = Meeting::getInstance()->meetingRoomGet($this->params);
		}

		$return = Response::getInstance()->gen_success($meeting_info);
		//会议室的地区
		$office_list = Office::getInstance()->officeList(array('page_size' => 50));
		$return['office_list'] = $office_list;
        return $this->app->response->setBody($return);

	}


	private function _init() {
		
		$this->rules = array(
			'room_id'  => array(
				'type'    => 'integer',
				'default' => 0, //0
			),

		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}