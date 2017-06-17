<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Department\DepartmentInfo;

/**
 * 添加部门
 * @author wulianglong
 * @date 2015-4-22 上午10:49:41
 */
class DeptInfoCreate extends BaseModule {
	
	private $deptinfo = NULL;
	
	public function run() {
		
		$this->_init();
		$not_null = array('depart_name', 'depart_level', 'parent_id');
		$result = DepartmentInfo::getInstance()->add($this->deptinfo, $not_null);
		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if($result === 0){
			$return = Response::gen_error(50003);
		}else {
			$return = Response::gen_success(array('msg' => '添加成功', 'depart_id' => $result));
		}
		
		$this->app->response->setBody($return);
	}
	
	
	private function _init() {
		
		$post = $this->request->POST;
		
		$this->rules = array(
			'depart_name' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'string',
				'maxLength'=> 30,
			),
			'depart_info' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'string',
				'maxLength'=> 300,
			),
			'depart_level' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
			),
			'parent_id' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
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
				'default' => 1,
			),
			'is_virtual' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
				'default' => 0,
			),
			'level' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
			),
			'status' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
				'default' => 1,
			),
		);
		
		$safe_post = $this->post()->safe();
		$this->deptinfo = array_intersect_key($safe_post, $post);
		return TRUE;
	}
	
}