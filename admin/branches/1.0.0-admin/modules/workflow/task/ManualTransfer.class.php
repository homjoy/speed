<?php 
namespace Admin\Modules\Workflow\Task;

/**
 * 任务手动转移
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class ManualTransfer extends BaseModule {

	private $task = NULL;
	private $errors = NULL;

	public function run() {

		$this->_init();

		//参数校验
		if($this->query()->hasError()){
			$return = Response::gen_error(10001, '', $this->errors);
			return $this->app->response->setBody($return);
		}
		
		if (empty($this->task['task_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}

		// 获取任务信息
		$task_info = self::getClient()->call('workflowatom', 'process/task_list', $this->task);
		$task_info = $this->parseApiData($task_info);

		// 获取任务类型信息
		$tasktype_list = self::getClient()->call('workflowatom', 'process/task_type_list', array());
		$tasktype_list = $this->parseApiData($tasktype_list);

		$tasktype_map = array();
		if ($tasktype_list !== FALSE) {
			foreach ($tasktype_list as $k => $v) {
				$tasktype_map[$v['type_id']] = $v['type_name'];
			}
		}

		// 任务流信息
		$process_list = array();
		if ($task_info !== FALSE) {

			$task_info = current($task_info);
			$process_list = self::getClient()->call('workflowatom', 'process/process_node_list', array(
				'type_id' => $task_info['tasktype_id'],
				'status'  => 1,
			));
			$process_list = $this->parseApiData($process_list);
		}

		// 审批流程信息
		$progress_list = self::getClient()->call('workflowatom', 'process/progress_list', $this->task);
		$progress_list = $this->parseApiData($progress_list);

		// 角色信息
		$role_list = self::getClient()->call('workflowatom', 'user/role_info_list', array('status' => 1));
		$role_list = $this->parseApiData($role_list);
		// var_dump($progress_list, $process_list);exit;
		$this->app->response->setBody(array(
			'taskInfo'     => $task_info,
			'progressList' => $progress_list,
			'tasktypeMap'  => $tasktype_map,
			'processList'  => $process_list,
			'roleList'     => $role_list,
		));
	}

	private function _init() {

		$this->rules = array(
			'task_id' => array(
				'required'  => TRUE,
				'type'      => 'integer',
				'maxLength' => 20,
			), 
		);

		$this->task = $this->query()->safe();
		$this->errors = $this->query()->getErrors();
	}
}