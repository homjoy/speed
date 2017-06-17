<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Department\DepartmentLeader;

/**
 * 添加部门领导
 * @author wulianglong
 * @date 2015-4-22 上午10:49:41
 */
class DeptLeaderCreate extends BaseModule {
	
	private $dept_leader = NULL;
	
	public function run() {
		
		$this->_init();
		$not_null = array('depart_id', 'user_id');
		$result = DepartmentLeader::getInstance()->add($this->dept_leader, $not_null);
		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if($result === 0){
			$return = Response::gen_error(50003);
		}else {
			$return = Response::gen_success(array('msg' => '添加成功', 'leader_id' => $result));
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
			'user_id' => array(
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
		$this->dept_leader = array_intersect_key($safe_post, $post);
		return TRUE;
	}
	
}