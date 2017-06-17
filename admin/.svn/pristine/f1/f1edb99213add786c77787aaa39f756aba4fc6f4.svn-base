<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 查找用户角色映射
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class AjaxSearchUserRoleMap extends BaseModule {

	protected $errors = NULL;
	protected $params = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		//参数校验
        if ($this->post()->hasError()) {
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        $queryParams = array();
        if (!empty($this->params['search_role'])) {
        	$queryParams['role_id'] = $this->params['search_role'];
        }
        if (!empty($this->params['search_user_id'])) {
        	$queryParams['user_id'] = $this->params['search_user_id'];
        }
        $queryParams['status'] = array(0, 1);

		$multiClient = self::getMultiClient();
        $multiClient->call('workflowatom', 'user/user_role_map_list', $queryParams, 'user_role');
        $multiClient->call('workflowatom', 'user/role_info_list', array('status' => array(0, 1)), 'role');
        $multiClient->callData();

        $user_role = $this->parseApiData($multiClient->user_role);
        $role = $this->parseApiData($multiClient->role);
       
        $role_map = array();
		if ($role !== FALSE) {
			foreach ($role as $k) {
				$role_map[$k['role_id']] = $k['role_name'];
			}
		}

		$userIds = array();
		if ($user_role !== FALSE) {
			foreach ($user_role as $k => $v) { 
				$userIds[] = $v['user_id'];
			}
		}

		$user = array();
		if (!empty($userIds)) {
			$user = self::getClient()->call('atom', 'account/get_user_info', array('user_id' => implode(',', $userIds), 'status' => array(1,2,3), 'all' => 1));
			$user = $this->parseApiData($user);
		}

		$user_map = array();
		if (!empty($user)) {
			foreach ($user as $k) {
				$user_map[$k['user_id']] = $k['name_cn'];
			}
		}

		if (!empty($user_role) && !empty($role_map) && !empty($user_map)) {
			foreach ($user_role as $k => $v) { 
				$v['role_name'] = isset($role_map[$v['role_id']]) ? $role_map[$v['role_id']] : '';
				$v['user_name'] = isset($user_map[$v['user_id']]) ? $user_map[$v['user_id']] : '';
				$user_role[$k] = $v;
			}
		}

		$return = Response::gen_success($user_role);
		$this->app->response->setBody($return);
	}

	private function _init() {

		$this->rules = array(
			'search_role' => array(
                'type' => 'integer',
			),
			'search_user_id' => array(
				'type' => 'integer',
			),
		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}