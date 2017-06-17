<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 待接收的任务列表
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-04
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class ReceptionTask extends BaseModule {

	private $params;

	public function run() {

		$this->_init();
		
		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}

		if (empty($this->params['user_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}

		// 获取当前用户具有的所有角色
		$my_role_list = self::getClient()->call('workflowatom', 'user/user_role_map_list', array(
			'user_id' => $this->params['user_id'],
		));
		$my_role_list = $this->parseApiData($my_role_list);

		$roleIds = array();
		if ($my_role_list !== FALSE) {
			foreach ($my_role_list as $k => $v) {
				$roleIds[] = $v['role_id'];
			}
		} 

		// 获取当前用户具有权限的所有节点
		$processIds = array();
		if (!empty($roleIds)) {
			$process_list = self::getClient()->call('workflowatom', 'process/process_node_list', array(
				'type_id' => $this->params['tasktype_id'],
				'status'  => 1,
			));
			$process_list = $this->parseApiData($process_list);

			if ($process_list !== FALSE) {
				foreach ($process_list as $k => $v) {
					$role = explode('_', $v['role_id']);
					if ($role[0] == 'wf') {
						if (in_array($role[1], $roleIds)) {
							$processIds[] = $v['process_id'];
						}	
					}	
				}
			}
		}

		// 获取当前用户具有审批权限且并未被分配的任务
		$result = array();
		if (!empty($processIds)) {
			$result = self::getClient()->call('workflowatom', 'process/task_list', array(
				'current_node_id' => $processIds,
				'tasktype_id'     => $this->params['tasktype_id'];
				'current_user_id' => 0,
				'flag'            => 1,
			));
			$result = $this->parseApiData($result);
		}

		$this->app->response->setBody(Response::gen_success($result));
		
	}

	private function _init() {

		$this->rules = array(
			'user_id' => array(
				'required'  => TRUE,
				'type'      => 'integer',
				'maxLength' => 11,
			), 
			'tasktype_id' => array(
				'required'  => TRUE,
				'type'      => 'integer',
				'maxLength' => 5,
			),
		);

		$this->params = $this->post()->safe();
	}
}