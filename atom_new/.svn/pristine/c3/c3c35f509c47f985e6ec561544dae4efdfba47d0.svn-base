<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Department\DepartmentInfo;

/**
 * 根据depart_id获取部门信息
 * @author wulianglong
 * @date 2015-5-26 下午4:38:32
 */
class DeptInfoGet extends BaseModule{
	
	private $depart_id = NULL;
	
	private $simple = array(
		'depart_id'    => 0,
		'depart_name'  => '',   //部门name
		'depart_info'  => '',   //部门信息
		'depart_level' => 0,    //部门级别
		'parent_id'    => 0,    //上级部门id
		'child_id'     => '',   //子部门id
		'memo'         => '',   //备注
		'update_time'  => '',
		'is_official'  => 0,    //是否为正式部门:0不是1是
		'is_virtual'   => 0,    //是否为虚拟部门:0不是1是
		'level'        => 0,    //级别
		'status'       => 0,    //状态:0无效1有效
	);

	public function run() {
		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}

		if (!empty($this->depart_id)) {
			$list = DepartmentInfo::getInstance()->getByPk($this->depart_id);
		}elseif (!empty($this->depart_name)) {
			$list = DepartmentInfo::getInstance()->getUseLike('depart_name', $this->depart_name, 1);
		}

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
		$this->depart_id = isset($data['depart_id']) ? intval($data['depart_id']) : 0;
		$this->depart_name = isset($data['depart_name']) ? trim($data['depart_name']) : '';
		if(!empty($this->depart_id) && !empty($this->depart_name)) {
			return FALSE;
		}
		return TRUE;
	}

}