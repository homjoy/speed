<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfo;

/**
 * 更新UpdateDeptInfo
 * @author hongzhou@meilishuo.com
 * @date 2015-8-26 
 */
class UpdateDeptInfo extends BaseModule {
	
	private $deptinfo = NULL;

	public function run() {
		
		$this->_init();
		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

        if(!isset($this->deptinfo['depart_id'])||empty($this->deptinfo['depart_id'])|| (count($this->deptinfo)<=1)){
            $return = Response::gen_error(10001, '','没有更新信息或者没有填写部门id');
            return $this->app->response->setBody($return);
		}
		$result = DepartmentInfo::model()->updateById($this->deptinfo);
		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)){
            $return  = array();
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
			'depart_name' => array(
				'type'=>'string',
				'maxLength'=> 30,
			),
			'depart_info' => array(
				'type'=>'string',
				'maxLength'=> 30,
			),
			'depart_level' => array(
				'type'=>'integer',
			),
			'parent_id' => array(
				'type'=>'integer',
				'maxLength'=> 30,
			),
			'child_id' => array(
				'type'=>'integer',
				'maxLength'=> 30,
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
		return TRUE;
	}
	
}