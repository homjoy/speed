<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\RoleInfo;
use Libs\Util\Format;

/**
 * 修改角色信息
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-15 
 */

class RoleInfoUpdate extends BaseModule {

	protected $role = array();
	protected $errors = array();
	private $sample;

	public function run() {

		$this->_init();

		$this->sample = RoleInfo::getInstance()->getFields();

		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        if (empty($this->role['role_id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        foreach ($this->role as $key => $value) {
            if (!empty($value)) {
                $queryParams[$key] = $value;
            }
        }
        if ($this->role['status'] !== '') {
            $queryParams['status'] = $this->role['status'];
        }
        
		$result = RoleInfo::getInstance()->update($queryParams);

		if ($result === FALSE) {
			$return =  Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50004);
		} else {
			$return = Response::gen_success(Format::outputData($this->role, $this->sample));
		}

		$this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {

		$this->rules = array(
			'role_id'   => array(
				'required'   => TRUE,
				'type'       => 'integer',
				'maxLength'  => 5,
			),
			'role_name' => array(
				'required'   => TRUE,
				'allowEmpty' => FALSE,
				'type'       => 'string',
				'maxLength'  => 30,
			),
			'status'    => array(
				'type'    => 'integer',
				'default' => 1,
			),
		);

		$this->role = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}