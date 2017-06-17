<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 添加用户角色映射
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class UserRoleMapEdit extends BaseModule {
	
	protected $errors = NULL;
	protected $user_role = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		//参数校验
        if ($this->post()->hasError()) {
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        if (empty($this->user_role['user_id']) || empty($this->user_role['role_id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        // 判断用户角色是否已存在
        $user_role_list = self::getClient()->call('workflowatom', 'user/user_role_map_list', array(
        	'user_id' => $this->user_role['user_id'],
        	'role_id' => $this->user_role['role_id'],
        	'status'  => array(0,1),
        ));
        $user_role_list = $this->parseApiData($user_role_list);

        if (!empty($this->user_role['map_id'])) {
        	unset($user_role_list[$this->user_role['map_id']]);
        }

        if (!empty($user_role_list)) {
			$return = Response::gen_error(10002, '用户角色已存在');
        	return $this->app->response->setBody($return);
        }

		$role = self::getClient()->call('workflowatom', 'user/role_info_get', array('role_id' => $this->user_role['role_id']));
		$role = $this->parseApiData($role);
		
		//若角色的有效性与映射的有效性应该保持一致
		if ($role['status'] == 0 && $this->user_role['status'] == 1) {
			$return = Response::gen_error(10004, '角色和映射的有效性不一致');
		} else {
			//map_id == 0为添加用户角色映射，否则为编辑用户角色映射
			if ($this->user_role['map_id'] == 0) {
				$result = self::getClient()->call('workflowatom', 'user/user_role_map_create', $this->user_role);
				$result = $this->parseApiData($result);
			} else {
				$result = self::getClient()->call('workflowatom', 'user/user_role_map_update', $this->user_role);
				$result = $this->parseApiData($result);
			}
		
			if ($result !== FALSE) {
				$return = Response::gen_success($result);
				$return['url'] = '/workflow/user/user_role_map_index';
			}
		}

		$this->app->response->setBody($return);
	}

	private function _init() {

		$this->rules = array(
			'map_id'  => array(
				'required'	=> TRUE,
                'type'		=> 'integer',
                'default'	=> 0,
			),
			'user_id' => array(
				'required'   => TRUE,
				'allowEmpty' => FALSE,
				'type'       => 'integer',
			),
			'role_id' => array(
				'required'   => TRUE,
				'allowEmpty' => FALSE,
				'type'       => 'integer',
			),
			'status'  => array(
				'type'       => 'integer',
				'default'	 => 1,
			),
		);

		$this->user_role = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}