<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 接受任务
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;
use WorkFlowApi\Package\Task\TaskTransfer;

class AcceptTask extends BaseModule {

	private $params;

	public function run() {

		$this->_init();

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
		}

		if (empty($this->params['task_id']) || empty($this->params['user_id'])) {
			$return = Response::gen_error(10001, 'task_id 或 user_id 为空');
        	return $this->app->response->setBody($return);
		}
		
		//task 信息
		$taskInfo = $this->getClient()->call('workflowatom', 'process/taskList', array(
			'task_id'	=> $this->params['task_id']
		));
		$taskInfo = $this->parseApiData($taskInfo);
		
		if($taskInfo === FALSE) {
			return;
		}
		
		$taskInfo = array_pop($taskInfo);
		
		if($taskInfo['status'] != 2) {
			
			if(!empty($taskInfo['current_user_id'])) {
				$return = Response::gen_error(10001, '该任务已被领取');
			}
			
        	return $this->app->response->setBody($return);
			
		}
		
		$multiClient = $this->getMultiClient();
		
		//processInfo
		$multiClient->call('workflowatom', 'process/GetProcessInfoById', array(
			'process_id'	=> $taskInfo['current_node_id']
		), 'processInfo');
		
		/*//speed用户信息
		$multiClient->call('atom', 'user/user_info_get', array(
			'user_id'	=> $this->params['user_id']
		), 'userInfo');
		
		//workflow user role map 信息
		$multiClient->call('workflowatom', 'user/userRoleMapList', array(
			'user_id'	=> $this->params['user_id']
		), 'roleMapInfo');*/
		
		$multiClient->callData();
		
		$processInfo = $this->parseApiData($multiClient->processInfo);
		if(FALSE === $processInfo) {
			return;
		}
		
		if(!empty($processInfo['role_id'])) {
			
			if(!empty($this->params['depart_id'])) {
				$queryParams = array(
					'depart_id'	=> $this->params['depart_id']
				);
			}else {
				$queryParams = array(
					'user_id'	=> $taskInfo['user_id']
				);
			}
			
			$nextUserIdArr = TaskTransfer::getNextUser($processInfo['role_id'], $queryParams);
			
			if(!in_array($this->params['user_id'], $nextUserIdArr)) {
				$return = Response::gen_error(10001, '该用户无权接收此任务');
				return $this->app->response->setBody($return);
			}
			
		}
		
		//请求更新任务状态
		$taskUpdateResult = $this->getClient()->call('workflowatom', 'process/taskUpdate', array(
			'taskParams'		=> array(
				'task_id'			=> $this->params['task_id'],
				'status'			=> 3,
				'current_user_id'	=> $this->params['user_id']
			),
			'progressParams'	=> array(
				'task_id'			=> $this->params['task_id'],
				'status'			=> 3,
				'current_user_id'	=> $this->params['user_id'],
				'action_type'		=> 0,
				'process_id'		=> $taskInfo['current_node_id'],
				'progress_content'	=> '接收任务',
			)
		));
		
		
		$taskUpdateResult = $this->parseApiData($taskUpdateResult);
		
		if ($taskUpdateResult !== FALSE) {
			$this->app->response->setBody(Response::gen_success('接收成功'));
		}

	}

	private function _init() {

		$this->rules = array(
			'task_id' => array(
				'required'   => TRUE,
				'type'       => 'integer'
			),
			'user_id' => array(
				'required'   => TRUE,
				'type'       => 'integer'
			),
			'depart_id' => array(
				'type'      => 'integer',
				'default'	=> 0
			)
		);

		$this->params = $this->post()->safe();
	}
}