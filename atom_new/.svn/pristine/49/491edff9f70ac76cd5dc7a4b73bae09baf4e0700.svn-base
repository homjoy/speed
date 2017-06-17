<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Department\DepartmentInfo;
/**
 * 修改部门
 * @author wulianglong
 * @date 2015-4-22 上午10:50:04
 */
class DeptInfoUpdate extends BaseModule {
	
	private $deptinfo = NULL;
	
	public function run() {
		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10004, '缺少修改字段'));
			return FALSE;
		}
		
		$where = array(
			'depart_id' => $this->deptinfo['depart_id'],
		);
		$result = DepartmentInfo::getInstance()->update($this->deptinfo, $where);
		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if($result === 0){
			$return = Response::gen_error(50012);
		}else {
			$return = Response::gen_success(array('msg' => '修改成功', 'affected' => $result));
		}
		
		$this->app->response->setBody($return);
	}
	
	
	private function _init() {
		$post = $this->request->POST;

		$this->rules = array(
			'depart_id' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
			),
			'depart_name' => array(
				'type'=>'string',
				'maxLength'=> 30,
			),
			'depart_info' => array(
				'type'=>'string',
				'maxLength'=> 300,
			),
			'depart_level' => array(
				'type'=>'integer',
			),
			'parent_id' => array(
				'type'=>'integer',
			),
			'child_id' => array(
				'type'=>'string',
				'maxLength'=> 300,
			),
			'memo' => array(
				'type'=>'string',
				'maxLength'=> 300,
			),
			'is_official' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
			),
			'is_virtual' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
			),
			'level' => array(
				'type'=>'integer',
			),
			'status' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
			),
		);		
		$safe_post = $this->post()->safe();
		$this->deptinfo = array_intersect_key($safe_post, $post);
		
		if(count($this->deptinfo) < 2) {
			return FALSE;
		}
		return TRUE;
	}
	
}