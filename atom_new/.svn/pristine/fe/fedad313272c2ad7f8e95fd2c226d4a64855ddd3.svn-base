<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentLeaderInfo;

/**
 * 部门更新
 * @author hongzhou@meilishuo.com
 * @date 2015-8-26 
 */
class 	UpdateDeptLeader extends BaseModule {
	
	private $deptleader = NULL;
    //private $sample  = NULL;

	public function run() {
		
		$this->_init();
		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
        //$this->sample = DepartmentLeaderInfo::model()->getFields();
        //var_dump($this->sample);die();

		if(empty($this->deptleader['depart_id'])){
            $return = Response::gen_error(10001, '','部门ID不能为空的');
            return $this->app->response->setBody($return);
		}
		$result = DepartmentLeaderInfo::model()->updateById($this->deptleader);

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)){
			$return = Response::gen_error(50003);
		}else {

			$return = Response::gen_success($result);
		}
		//var_dump($return);die();
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