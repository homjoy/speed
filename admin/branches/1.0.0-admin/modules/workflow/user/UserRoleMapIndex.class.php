<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 展示所有用户角色映射
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-13
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class UserRoleMapIndex extends BaseModule {

	protected $checkUserPermission = TRUE;

	public function run() {

		$multiClient = self::getMultiClient();
		$multiClient->call('workflowatom', 'user/user_role_map_list', array('status' => array(0, 1)), 'user_role');
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

		if (!empty($user_role)) {
			foreach ($user_role as $k => $v) { 
				$v['role_name'] = isset($role_map[$v['role_id']]) ? $role_map[$v['role_id']] : '';
				$v['user_name'] = isset($user_map[$v['user_id']]) ? $user_map[$v['user_id']] : '';
				$user_role[$k] = $v;
			}
		}

		$this->app->response->setBody(array(
			'userRole' => $user_role,
			'roleInfo' => $role,
		));
	}
}