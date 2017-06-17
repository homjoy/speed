<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentLeaderInfo;

/**
 * 添加部门
 * @author hongzhou@meilishuo.com
 * @date 2015-8-26 
 */
class CreateDeptLeader extends BaseModule {
	
	private $deptleader = NULL;

	public function run() {
		
		$this->_init();
		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

		if(!isset($this->deptleader['depart_id'])|| !isset($this->deptleader['user_id'])){
            $return = Response::gen_error(10001, '','用户ID或部门ID不能为空的');
            return $this->app->response->setBody($return);
		}
		$result = DepartmentLeaderInfo::model()->insert($this->deptleader);

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)){
			$return = Response::gen_error(50003);
		}else {
			$return = Response::gen_success($result);
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
				'maxLength'=> 30,
			),
			'user_id' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
				'maxLength'=> 30,
			),
			'update_time' => array(
				'type'=>'string',
			),
			'status' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
				'default' => 1,
			),
		);
		
		$safe_post = $this->post()->safe();
		$this->deptleader = array_intersect_key($safe_post, $post);
		return TRUE;
	}
	
}