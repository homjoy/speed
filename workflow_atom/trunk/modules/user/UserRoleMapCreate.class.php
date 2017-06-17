<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserRoleMap;
use Libs\Util\Format;

/**
 * 添加用户角色映射
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-15 
 */

class UserRoleMapCreate extends BaseModule {

	protected $user_role = array();
	protected $errors = array();
	private $sample;

	public function run() {

		$this->_init();

		$this->sample = UserRoleMap::getInstance()->getFields();

		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        if (empty($this->user_role['user_id']) || empty($this->user_role['role_id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }
        
		$result = UserRoleMap::getInstance()->insert($this->user_role);

		if ($result === FALSE) {
			$return =  Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50003);
		} else {
			$this->user_role['map_id'] = $result;
			$return = Response::gen_success(Format::outputData($this->user_role, $this->sample));
		}

		$this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {

		$this->rules = array(
			'user_id' => array(
				'required'   => TRUE,
                'type'       => 'integer',
                'maxLength'  => 11,
			),
			'role_id' => array(
				'required'   => TRUE,
                'type'       => 'integer',
                'maxLength'  => 5,
			),
			'status'  => array(
				'type'    => 'integer',
				'default' => 1,
			),
		);

		$this->user_role = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}