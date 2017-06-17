<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 撤销任务
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class RevokeTask extends BaseModule {

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
		
		if($taskInfo['user_id'] != $this->params['user_id']) {
			$return = Response::gen_error(10001, '该用户无权操作');
			return $this->app->response->setBody($return);
		}
		
		if (!isset($this->params['progress_content']) || empty($this->params['progress_content'])) {
			$this->params['progress_content'] = '申请人撤销';
		}

		$taskUpdateResult = $this->getClient()->call('workflowatom', 'process/taskUpdate', array(
			'taskParams'		=> array(
				'task_id'			=> $taskInfo['task_id'],
				'status'			=> 6
			),
			'progressParams'	=> array(
				'task_id'			=> $taskInfo['task_id'],
				'status'			=> 6,
				'current_user_id'	=> $this->params['user_id'],
				'action_type'		=> 0,
				'process_id'		=> $taskInfo['current_node_id'],
				'progress_content'	=> $this->params['progress_content'],
			)
		));
		$taskUpdateResult = $this->parseApiData($taskUpdateResult);
		
		if($taskUpdateResult === FALSE) {
			return;
		}
		
		$this->app->response->setBody(Response::gen_success('撤销成功'));
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
			'progress_content' =>array(
				'type' => 'string', 
			),
		);

		$this->params = $this->post()->safe();
	}
}