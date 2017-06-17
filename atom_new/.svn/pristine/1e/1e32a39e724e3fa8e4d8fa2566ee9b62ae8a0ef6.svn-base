<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentSubTemp;

/**
 * 添加部门领导人替换备份
 * @author hongzhou@meilishuo.com
 * @date 2015-09-24
 */
class CreateDepartSubTemp extends BaseModule {

	private $deptinfo = NULL;

	public function run() {
		
		$this->_init();
		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

		if(!isset($this->deptinfo['relation_id'])||empty($this->deptinfo['user_id'])){
            $return = Response::gen_error(10001, '','user_id和relation_id不能为空');
            return $this->app->response->setBody($return);
		}

		$result = DepartmentSubTemp::model()->insert($this->deptinfo);
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
			'relation_id' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
				'type'=>'integer',
			),
			'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
			),
			'memo' => array(
				'type'=>'string',
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