<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfoTemp;
use Atom\Package\Account\DepartmentRelationTemp;
use Atom\Package\Account\DepartmentSubTemp;
use Atom\Package\Account\DepartmentInfo;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentSub;

/**
 * 根据temp信息，创建部门所有信息
 * @author minggeng@meilishuo.com
 * @date 2015-10-9
 */
class CreateAllDepartInfoByTemp extends BaseModule {

    protected $data = array();
    private $token = 'd1a7f6b8345f149f53be207e1d6d26a6';

	public function run() {
		
		$this->_init();
		$this->_check();

        //清空主表
        $result = DepartmentInfo::model()->deleteAll();
        $result = DepartmentRelation::model()->deleteAll();
        $result = DepartmentSub::model()->deleteAll();

        //temp表信息覆盖主表
        $result = DepartmentInfo::model()->insertAll();
        $result = DepartmentRelation::model()->insertAll();
        $result = DepartmentSub::model()->insertAll();

        //清空temp表
        $result = DepartmentInfoTemp::model()->deleteAll();
        $result = DepartmentRelationTemp::model()->deleteAll();
        $result = DepartmentSubTemp::model()->deleteAll();

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)){
			$return = Response::gen_error(50003);
		}else {
			$return = Response::gen_success($result);
		}
		$this->app->response->setBody($return);
	}

    private function _check(){
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        if(!isset($this->data['token']) || empty($this->data['token'])){
            $return = Response::gen_error(10001, '','token不能为空的');
            return $this->app->response->setBody($return);
        }
        if($this->data['token'] != $this->token){
            $return = Response::gen_error(10001, '','token信息异常');
            return $this->app->response->setBody($return);
        }
    }
	
	private function _init() {
        $this->rules = array(
            'token'			=> array(
                'required'	=> true,
                'type'		=> 'string',
            ),
        );
        $this->data = $this->post()->safe();
	}
	
}