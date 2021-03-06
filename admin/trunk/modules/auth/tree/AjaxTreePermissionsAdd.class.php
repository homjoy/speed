<?php
namespace Admin\Modules\Auth\Tree;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Libs\Util\Format;

class AjaxTreePermissionsAdd extends BaseModule {
	
	private $params = array();
	private $errors = array();
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
        	return $this->app->response->setBody($return);
		}

		if (empty($this->params['role_id']) || empty($this->params['tree_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}

		$multiClient = self::getMultiClient();
		$multiClient->call('workflowatom', 'tree/tree_by_param', array('tree_id' => $this->params['tree_id'], 'status' => array(0, 1)), 'tree');
		$multiClient->call('workflowatom', 'treerole/role_by_param', array('role_id' => $this->params['role_id'], 'status' => array(0, 1)), 'role');
		$multiClient->callData();

		$tree = $this->parseApiData($multiClient->tree);
		$role = $this->parseApiData($multiClient->role);

		if (empty($tree)) {
			$res = array(
				'code' => 400,
				'error_msg' => '菜单信息获取失败，请重新尝试',
			);

			return $this->app->response->setBody($res);
		}

		if (empty($role)) {
			$res = array(
				'code' => 400,
				'error_msg' => '角色信息获取失败，请重新尝试',
			);

			return $this->app->response->setBody($res);
		}

		$tree = current($tree);
		$role = current($role);

		//判断tree、role、permission有效性，若tree或role无效，permission应为无效
		if (($tree['status'] == 0 || $role['status'] == 0) && $this->params['status'] == 1) {
			$res = array(
				'code' => 400,
				'error_msg' => 'tree、role、permission有效性不一致',
			);

			return $this->app->response->setBody($res);
		}
		
		if (!empty($tree)) {
			$this->params['parent_id'] = $tree['parent_id'];
		}

		$result = self::getClient()->call('workflowatom', 'tree/tree_permissions_add', $this->params);
		$res = array();

		if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
			$res = array(
				'code' => 200,
				'url' => '/auth/tree/tree_permissions',
			);
		} else if ($result['httpcode'] == 200 && isset($result['content']['error_code'])) {
			$res = array(
				'code' => 400,
				'error_msg' => $result['content']['error_detail'],
			);
		} else {
			$res = array(
				'code' => 400,
				'error_msg' => '操作失败',
			);
		}

    	$this->app->response->setBody($res);
	}
	
	protected function _init() {
		
		$this->rules = array(
			'id'        => array(
				'required' => true,
                'type'	   => 'integer',
                'default'  => 0,
			),
			'role_id'	=> array(
                'required' => true,
                'type'	   => 'integer',
                'default'  => 0,
            ),
            'tree_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'   => 0,
            ),
            'status'	=> array(
                'type'	  => 'integer',
                'default' => 1,
            ),
        );
		
		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}