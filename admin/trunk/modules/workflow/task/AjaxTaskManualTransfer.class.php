<?php 
namespace Admin\Modules\Workflow\Task;

/**
 * 任务手动转移
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class AjaxTaskManualTransfer extends BaseModule {

	private $params = NULL;
	private $errors = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {

		$this->_init();

		// 参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
			return $this->app->response->setBody($return);
		}

		if (empty($this->params['task_id']) && empty($this->params['process_id']) && empty($this->params['user_id'])) {
			$return = Response::gen_error(10001);
			return $this->app->response->setBody($return);
		}

		$task_info = self::getClient()->call('workflowatom', 'process/task_list', array('task_id' => $this->params['task_id']));
		$task_info = $this->parseApiData($task_info);
		$task_info = current($task_info);

		$params = array();

		// 更新task参数
		$taskParams['task_id'] = $this->params['task_id'];
		$taskParams['current_user_id'] = $this->params['user_id'];
		$taskParams['current_node_id'] = $this->params['process_id'];
		// 插入progress参数
		$progressParams['task_id'] = $this->params['task_id'];
		$progressParams['process_id'] = $this->params['process_id'];
		$progressParams['action_type'] = 0;
		$progressParams['current_user_id'] = $this->user['id'];
		$progressParams['status'] = $task_info['status']; 
		$progressParams['progress_content'] = "手动转移";

		$params['taskParams'] = $taskParams;
		$params['progressParams'] = $progressParams;

		$result = self::getClient()->call('workflowatom', 'process/task_update', $params);
		$result = $this->parseApiData($result);

		if ($result !== FALSE) {
			$return = Response::gen_success($result);
			$this->app->response->setBody($return);
		}
	}	

	private function _init() {

		$this->rules = array(
			'task_id'    => array(
				'required'	=> TRUE,
                'type'		=> 'integer',
                'default'	=> 0,
			),
			'process_id' => array(
				'required'	=> TRUE,
                'type'		=> 'integer',
                'default'	=> 0,
			),
			'user_id'    => array(
				'required'	=> TRUE,
                'type'		=> 'integer',
                'default'	=> 0,
			),
		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}