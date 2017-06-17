<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 处理任务
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;
use WorkFlowApi\Package\Task\TaskTransfer;

class ProcessTask extends BaseModule {

	private $params;

	public function run() {

		$this->_init();
		
		//参数校验
		if ($this->post()->hasError()) {
			$this->app->log->log('process_task:ProcessTask', __LINE__ . var_export($this->post()->getErrors(), true));
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
		}

		if (empty($this->params['task_id']) || empty($this->params['user_id']) || empty($this->params['action_type'])) {
			$this->app->log->log('process_task:ProcessTask', __LINE__ . ' task_id 或 user_id 或 action_type 为空!' . var_export($this->params, true));
			$return = Response::gen_error(10001, 'task_id 或 user_id 或 action_type 为空');
        	return $this->app->response->setBody($return);
		}
		
		//task 信息
		$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/taskList 开始!');
		$taskInfo = $this->getClient()->call('workflowatom', 'process/taskList', array(
			'task_id'	=> $this->params['task_id']
		));
		$taskInfo = $this->parseApiData($taskInfo);
		$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/taskList 结束!');

		if($taskInfo === FALSE) {
			$this->app->log->log('process_task:ProcessTask', __LINE__ . ' task_info获取失败!' . var_export($this->params, true));
			return;
		}
		
		$taskInfo = array_pop($taskInfo);
		
		//获取任务节点列表
		$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/process_node_list 开始!');
		$processInfo = self::getClient()->call('workflowatom', 'process/process_node_list', array(
			'type_id' 	=> $taskInfo['tasktype_id'],
			'status'	=> 1
		));
		$processInfo = $this->parseApiData($processInfo);
		$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/process_node_list 结束!');
		
		if($processInfo === FALSE) {
			$this->app->log->log('process_task:ProcessTask', __LINE__ . ' process_info获取失败!' . var_export($this->params, true));
			return;
		}elseif(empty($processInfo)) {
			$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 任务流程节点为空!' . var_export($this->params, true));
			$return = Response::gen_error(10001, '', '任务流程节点为空');
			return $this->app->response->setBody($return);
		}
		
		//接收任务
		if('accept' == $this->params['operate'] || 'all' == $this->params['operate']) {
			
			if(2 == $taskInfo['status']) {
				if(!empty($processInfo[$taskInfo['current_node_id']]['role_id'])) {
				
					if(!empty($this->params['depart_id'])) {
						$queryParams = array(
							'depart_id'	=> $this->params['depart_id']
						);
					}else {
						$queryParams = array(
							'user_id'	=> $taskInfo['user_id']
						);
					}
					
					TaskTransfer::$uid = $this->params['speed_user_id'];
					$nextUserIdArr = TaskTransfer::getNextUser($processInfo[$taskInfo['current_node_id']]['role_id'], $queryParams);
					
					if(!in_array($this->params['user_id'], $nextUserIdArr)) {
						$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 该用户无权接收此任务!' . var_export($this->params, true));
						$return = Response::gen_error(10001, '该用户无权接收此任务');
						return $this->app->response->setBody($return);
					}
					
				}
				
				//请求更新任务状态
				$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/taskUpdate 开始!');
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
				$this->app->log->log('process_task: atom_interface', __LINE__ . ' 访问 process/taskUpdate 结束!');
				
				if ($taskUpdateResult === FALSE) {
					$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 用户更新任务失败!' . var_export($this->params, true));
					return;
				}
				
				if('accept' == $this->params['operate']) {
					return $this->app->response->setBody(Response::gen_success(array(
						'status'	=> 3
					)));
				}
				
				//更新数据
				$taskInfo['status'] = 3;
				$taskInfo['current_user_id'] = $this->params['user_id'];
				
			}elseif(!empty($taskInfo['current_user_id']) && $taskInfo['current_user_id'] != $this->params['user_id']) {
				$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 该任务已被领取!' . var_export($this->params, true));
				$return = Response::gen_error(10001, '该任务已被领取');
				return $this->app->response->setBody($return);
				
			}
		}
		
		//处理任务
		if('process' == $this->params['operate'] || 'all' == $this->params['operate']) {
			if(3 != $taskInfo['status']) {
				$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 任务状态有误!' . var_export($this->params, true));
				$return = Response::gen_error(10001, '任务状态有误');
				return $this->app->response->setBody($return);
				
			}
			
			if($taskInfo['current_user_id'] != $this->params['user_id']) {
				$this->app->log->log('process_task:ProcessTask', __LINE__ . ' 该用户无权操作!' . var_export($this->params, true));
				$return = Response::gen_error(10001, '该用户无权操作');
				return $this->app->response->setBody($return);
				
			}
			
			$transferInfo = TaskTransfer::operateTransfer(array(
				'taskInfo'			=> $taskInfo,
				'processInfo'		=> $processInfo,
				'operator'			=> $this->params['user_id'],
				'action_type'		=> $this->params['action_type'],
				'progress_content'	=> $this->params['progress_content'],
				'ruleExtendParams'	=> $this->params['ruleExtendParams'],
				'depart_id'			=> $this->params['depart_id']
			));
			
			if(!empty($transferInfo['error_msg'])) {
				$this->app->log->log('process_task:ProcessTask', __LINE__ . $transferInfo['error_msg'] . var_export($this->params, true));
				$this->app->response->setBody(Response::gen_error($transferInfo['error_code'], $transferInfo['error_msg']));
			}
			
			$this->app->response->setBody(Response::gen_success(array(
				'status'	=> $transferInfo
			)));
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
			'action_type' => array(
				'required'   => TRUE,
				'type'       => 'integer'
			),
			'progress_content' => array(
				'required'   => TRUE,
				'type'       => 'string'
			),
			'depart_id' => array(
				'type'      => 'integer',
				'default'	=> 0
			),
			'operate' => array(
				'type'      => 'string',
				'default'	=> 'process'
			),
			'speed_user_id' => array(
				'required'   => TRUE,
				'type'       => 'integer'
			),
		);

		$this->params = $this->post()->safe();
		
		$this->params['ruleExtendParams'] = isset($this->request->POST['ruleExtendParams']) ? $this->request->POST['ruleExtendParams'] : array();
	}
}