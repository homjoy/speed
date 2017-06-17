<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Department\DepartmentLeader;
/**
 * 修改部门领导
 * @author wulianglong
 * @date 2015-4-22 上午10:50:04
 */
class DeptLeaderUpdate extends BaseModule {
	
	private $dept_leader = NULL;
	
	public function run() {
		
		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10004, '缺少修改字段'));
			return FALSE;
		}
		
		$where = array(
			'depart_id' => $this->dept_leader['depart_id'],
		);
		$result = DepartmentLeader::getInstance()->update($this->dept_leader, $where);
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
			'user_id' => array(
				'type'=>'integer',
			),
			'status' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
			),
		);
		
		$safe_post = $this->post()->safe();
		$this->dept_leader = array_intersect_key($safe_post, $post);
		
		if(count($this->dept_leader) < 2) {
			return FALSE;
		}
		return TRUE;
	}
	
}