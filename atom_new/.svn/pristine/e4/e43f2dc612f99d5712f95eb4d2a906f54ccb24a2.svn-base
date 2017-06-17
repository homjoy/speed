<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfo;

/**
 * 添加部门CreateDepartInfo
 * @author hongzhou@meilishuo.com
 * @date 2015-8-26 
 */
class CreateDepartInfo extends BaseModule {
	
	private $deptinfo = NULL;

	public function run() {
		
		$this->_init();
		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

		if(!isset($this->deptinfo['depart_name'])||empty($this->deptinfo['depart_name'])){
            $return = Response::gen_error(10001, '','用户名不能为空的');
            return $this->app->response->setBody($return);
		}
        if(isset($this->deptinfo['depart_name'])){
            $this->deptinfo['depart_name'] =htmlspecialchars_decode(  $this->deptinfo['depart_name']);
        }
        if(isset($this->deptinfo['depart_info'])){
            $this->deptinfo['depart_info'] =htmlspecialchars_decode(  $this->deptinfo['depart_info']);
        }
		$result = DepartmentInfo::model()->insert($this->deptinfo);
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
			'depart_name' => array(
				'required' => TRUE,
				'allowEmpty' => FALSE,
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
				'default' => 1,
			),
			'is_virtual' => array(
				'type'=>'integer',
				'enum'=> array(0,1),
				'default' => 0,
			),
			'level' => array(
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