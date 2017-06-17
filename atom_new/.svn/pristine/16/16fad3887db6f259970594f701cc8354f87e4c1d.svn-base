<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Department\DepartmentInfo;

/**
 * 获取部门信息 或 批量获取
 * @author wulianglong
 * @date 2015-4-22 上午10:56:25
 */
class DeptInfoBget extends BaseModule{
	
	private $depart_id = NULL;
	private $depart_level = NULL;
	private $parent_id = NULL;
	private $level = NULL;
	
	private $want = NULL;
	private $what = NULL;
	private $type = NULL;
	
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

		$this->_init();
		$ready = $this->ready();
		if(!$this->ready()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}
		
		$list = DepartmentInfo::getInstance()->getUseIn($this->want, $this->what, $this->type, array(), 'depart_id');
		if($list === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($list)) {
			$return = Response::gen_error(50002);
		}else {
			$return = Response::gen_success(Format::outputData($list, $this->simple, TRUE));
		}
		
		$this->app->response->setBody($return);
	}
	
	//获取到一个即可
	private function ready() {
	
		if(!empty($this->depart_id)) {
			$this->type = 'int';
			$this->what = 'depart_id';
			$this->want = explode(',', $this->depart_id);
			return TRUE;
	
		}else if(!empty($this->depart_level)) {
			$this->type = 'int';
			$this->what = 'depart_level';
			$this->want = explode(',', $this->depart_level);
			return TRUE;
				
		}else if(!empty($this->parent_id)) {
			$this->type = 'int';
			$this->what = 'parent_id';
			$this->want = explode(',', $this->parent_id);
			return TRUE;
			
		}else if(!empty($this->level)) {
			$this->type = 'int';
			$this->what = 'level';
			$this->want = explode(',', $this->level);
			return TRUE;
		}
	
		return FALSE;
	}
	
	private function _init() {
		
		$data = $this->request->GET;
		$this->depart_id = isset($data['depart_id']) ? trim($data['depart_id']) : '';
		$this->depart_level = isset($data['depart_level']) ? trim($data['depart_level']) : '';
		$this->parent_id = isset($data['parent_id']) ? trim($data['parent_id']) : '';
		$this->level = isset($data['level']) ? trim($data['level']) : '';
		
		return TRUE;
	}

}