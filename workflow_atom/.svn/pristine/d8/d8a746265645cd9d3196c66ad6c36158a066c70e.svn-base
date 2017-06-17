<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\RoleInfo;
use Libs\Util\Format;

/**
 * 角色信息列表
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-15 
 */

class RoleInfoList extends BaseModule {

	protected $role;
	protected $errors = array();
	private $sample;

	public function run() {

		$this->_init();

		$this->sample = RoleInfo::getInstance()->getFields();

		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        $queryParams = array();
        if (!empty($this->role['role_id'])) {
        	$queryParams['role_id'] = $this->role['role_id'];
        } 
        if (!empty($this->role['role_name'])) {
        	$queryParams['role_name'] = $this->role['role_name'];
        }
        if ($this->role['status'] !== '') {
        	$queryParams['status'] = $this->role['status'];
        }

		$result = RoleInfo::getInstance()->getDataList($queryParams);

		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result) && !is_array($result)) {
        	$return = Response::gen_error(50002);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

        $this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {

		$this->rules = array(
			'role_id'   => array(
				'type' => 'integer',
			),
			'role_name' => array(
				'type' => 'string',
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