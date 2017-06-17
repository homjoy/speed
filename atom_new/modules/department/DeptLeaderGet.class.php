<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Department\DepartmentLeader;

/**
 * 获取部门领导
 * @author wulianglong
 * @date 2015-4-22 上午10:56:25
 */
class DeptLeaderGet extends BaseModule{
	
	private $depart_id = NULL;
	private $user_id = NULL;
	private $status = NULL;
	
	private $simple = array(
		'leader_id'   => 0,
		'depart_id'   => 0,    //部门id
		'user_id'     => 0,    //用户id
		'update_time' => '',
		'status'      => 0,    //状态:0无效1有效
	);

	public function run() {

		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}
		$where = array(
			'status'    => $this->status,
		);
		if (!empty($this->depart_id)) {
			$where['depart_id'] = $this->depart_id;
		}
		if (!empty($this->user_id)) {
			$where['user_id'] = $this->user_id;
		}
		$list = DepartmentLeader::getInstance()->getList($where);

		if($list === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($list)) {
			$return = Response::gen_error(50002);
		}else {
			$return = Response::gen_success(Format::outputData($list, $this->simple, TRUE));
		}

		$this->app->response->setBody($return);
	}

	private function _init() {

		$data = $this->request->GET;
		$this->depart_id = isset($data['depart_id']) ? intval(trim($data['depart_id'])) : 0;
		$this->user_id = isset($data['user_id']) ? intval(trim($data['user_id'])) : 0;
		$this->status = isset($data['status']) ? intval(trim($data['status'])) : 1;

		if(empty($this->depart_id) && empty($this->user_id)) {
			return FALSE;
		}
		
		$this->status = $this->status === 0 ? 0 : 1;
		
		return TRUE;
	}

}