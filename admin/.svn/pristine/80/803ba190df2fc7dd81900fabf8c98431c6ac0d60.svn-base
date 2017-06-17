<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 添加角色信息
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-09
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class RoleInfoEdit extends BaseModule {

	protected $errors = NULL;
	private $role = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;
	
	public function run() {

		$this->_init();

		//参数校验
        if ($this->post()->hasError()) {
        	$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
        }

        $role_list = self::getClient()->call('workflowatom', 'user/role_info_list', array(
        	'role_name' => $this->role['role_name'],
        	'status'    => array(0, 1),
        ));
        $role_list = $this->parseApiData($role_list);    

		//role_id == 0为添加角色信息，否则为编辑角色信息
		if ($this->role['role_id'] == 0) {

			if (!empty($role_list)) {
	        	$return = Response::gen_error(10002, '角色已存在');
	        	return $this->app->response->setBody($return);
	        }

			$result = self::getClient()->call('workflowatom', 'user/role_info_create', $this->role);
			$result = $this->parseApiData($result);
		} else {

			unset($role_list[$this->role['role_id']]);

			if (!empty($role_list)) {
	        	$return = Response::gen_error(10002, '角色已存在');
	        	return $this->app->response->setBody($return);
	        } 

			$role = self::getClient()->call('workflowatom', 'user/role_info_get', array('role_id' => $this->role['role_id']));
			$role = $this->parseApiData($role);
			
			$result = self::getClient()->call('workflowatom', 'user/role_info_update', $this->role);
			$result = $this->parseApiData($result);
		
			//如果角色变为无效，角色对应的用户角色映射也变为无效
			if (($result !== FALSE) && ($role !== FALSE) && ($role['status'] == 1) && ($result['status'] == 0)) {
				$params = array(
					'role_id' => $this->role['role_id'],
					'status'  => 1,
				);
				
				$map = self::getClient()->call('workflowatom', 'user/user_role_map_list', $params);
				$map = $this->parseApiData($map);

				$mapIds = array();
				if ($map !== FALSE) {
					foreach ($map as $k) {
						$mapIds[] = $k['map_id'];
					}
				}
			
				self::getClient()->call('workflowatom', 'user/user_role_map_delete', array('map_id' => $mapIds));
			}
		}	

		if ($result !== FALSE) {
			$this->app->response->setBody(Response::gen_success($result));
		} 
	}

	private function _init() {

		$this->rules = array(
			'role_id'	=> array(
                'required'	=> TRUE,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
			'role_name' => array(
				'required'   => TRUE,
				'allowEmpty' => FALSE,
				'type'       => 'string',
				'maxLength'  => 30,
			),
			'status'    => array(
				'type'       => 'integer',
				'default'    => 1,
			),
		);

		$this->role = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}