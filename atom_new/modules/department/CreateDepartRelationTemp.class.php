<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentRelationTemp;

/**
 * 添加部门关系备份
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-24
 */
class CreateDepartRelationTemp extends BaseModule {
	
	private $params = array();

	public function run() {
		
		$this->_init();

		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

		$result = DepartmentRelationTemp::model()->insert($this->params);

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
			),
			'user_id' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
			),
            'parent_relation_id' => array(
                'type'=>'integer',
            ),
            'role_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
            'is_virtual' => array(
                'type'=>'integer',
            ),
			'update_time' => array(
				'type'=>'string',
			),
			'status' => array(
				'type'=>'integer',
				'default' => 1,
			),
		);
		
		$safe_post = $this->post()->safe();
		$this->params = array_intersect_key($safe_post, $post);
		return TRUE;
	}
	
}