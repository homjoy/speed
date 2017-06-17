<?php 
namespace Admin\Modules\Workflow\Task;

/**
 * 任务详情
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-05
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Libs\Util\ArrayUtilities;

class ShowTaskDetail extends BaseModule {

	private $task = NULL;
	private $errors = NULL;
	// public static $VIEW_SWITCH_JSON = TRUE;

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

		// 获取审批流程日志列表、任务信息、任务类型列表
		$multiClient = self::getMultiClient();
		$multiClient->call('workflowatom', 'process/progress_list', $this->task, 'progress_list');
		$multiClient->call('workflowatom', 'process/task_list', $this->task, 'task_info');
		$multiClient->call('workflowatom', 'process/task_type_list', array(), 'tasktype_list');
		$multiClient->callData();

		$progress_list = $this->parseApiData($multiClient->progress_list);
		$task_info = $this->parseApiData($multiClient->task_info);
		$tasktype_list = $this->parseApiData($multiClient->tasktype_list);

		// 绑定用户名、任务名称、类型名称、流程节点名称等信息
		if ($progress_list !== FALSE && $task_info !== FALSE) {

			$processIds = $userIds = array();
			foreach ($progress_list as $k => $v) {
				if ($v['process_id'] != 0) {
					$processIds[] = $v['process_id'];
				}
				$userIds[] = $v['current_user_id'];
			}
			foreach ($task_info as $k => $v) {
				$processIds[] = $v['current_node_id'];
				$userIds[] = $v['user_id'];
				$userIds[] = $v['current_user_id'];
			}
 
			$multiClient = self::getMultiClient();
			$process = $process_map = $user = $user_avatar = $departIds = $user_map = array();
			if (!empty($processIds)) {
				$processIds = array_unique($processIds);
				$multiClient->call('workflowatom', 'process/process_node_list', array('process_id' => $processIds), 'process');
			}

			if (!empty($userIds)) {
				$userIds = array_unique($userIds);
				$multiClient->call('atom', 'account/get_user_info', array('user_id' => implode(',', $userIds), 'status' => array(1, 2, 3), 'all' => 1), 'user');
				$multiClient->call('atom', 'account/get_avatar', array('user_id' => implode(',', $userIds)), 'user_avatar');
			}

			$multiClient->callData();

			$process = $this->parseApiData($multiClient->process);
			$user = $this->parseApiData($multiClient->user);
			$user_avatar = $this->parseApiData($multiClient->user_avatar);

			if (!empty($process)) {
				foreach ($process as $k => $v) {
					$process_map[$v['process_id']] = $v['process_name'];
				}
			}

			if (!empty($userIds)) {
				foreach ($userIds as $k) {
					$user_map[$k]['name'] = isset($user[$k]['name_cn']) ? $user[$k]['name_cn'] : '';
					$user_map[$k]['depart_id'] = isset($user[$k]['depart_id']) ? $user[$k]['depart_id'] : '';
					$user_map[$k]['avatar'] = isset($user_avatar[$k]['avatar_small_full']) ? $user_avatar[$k]['avatar_small_full'] : '';
				}
			}

			$departIds = ArrayUtilities::my_array_column($user, 'depart_id');
			$depart = $depart_map = array();
			if (!empty($departIds)) {
				$departIds = array_unique($departIds);
				$depart = self::getClient()->call('atom', 'department/depart_info_list', array('depart_id' => $departIds, 'all' => 1));
				$depart = $this->parseApiData($depart);
			}

			if (!empty($depart)) {
				foreach ($depart as $k => $v) {
					$depart_map[$v['depart_id']] = $v['depart_name'];
				}
			}

			$status_map = array(
				'1' => '新建',
				'2' => '待接收',
				'3' => '处理中',
				'4' => '完成',
				'5' => '挂起',
				'6' => '撤销',
				'0' => '失效',
			);

			foreach ($progress_list as $k => $v) {

				$current_user_depart = isset($depart_map[$user_map[$v['current_user_id']]['depart_id']]) ? $depart_map[$user_map[$v['current_user_id']]['depart_id']] : '';
				$current_user_name = isset($user_map[$v['current_user_id']]['name']) ? $user_map[$v['current_user_id']]['name'] : '';
				$v['current_user_name'] = $current_user_depart . '-' . $current_user_name;
				$v['current_user_avatar'] = isset($user_map[$v['current_user_id']]['avatar']) ? $user_map[$v['current_user_id']]['avatar'] : DEFAULT_PHOTO;
				if ($v['process_id'] != 0) {
					$v['process_name'] = isset($process_map[$v['process_id']]) ? $process_map[$v['process_id']] : '';
				} else {
					$v['process_name'] = '创建任务';
				}
				$v['status_name'] = isset($status_map[$v['status']]) ? $status_map[$v['status']] : '';

				$progress_list[$k] = $v;
			}

			foreach ($task_info as $k => $v) {

				$user_depart = isset($depart_map[$user_map[$v['user_id']]['depart_id']]) ? $depart_map[$user_map[$v['user_id']]['depart_id']] : '';
				$user_name = isset($user_map[$v['user_id']]['name']) ? $user_map[$v['user_id']]['name'] : '';
				
				$v['user_name'] = $user_depart . '-' . $user_name;

				if ($v['current_user_id'] != 0) {
					$current_user_depart = isset($depart_map[$user_map[$v['current_user_id']]['depart_id']]) ? $depart_map[$user_map[$v['current_user_id']]['depart_id']] : '';
					$current_user_name = isset($user_map[$v['current_user_id']]['name']) ? $user_map[$v['current_user_id']]['name'] : '';
					$v['current_user_name'] = $current_user_depart . '-' . $current_user_name;
				} else {
					$v['current_user_name'] = '未开始审核';
				}
				
				$v['current_node_name'] = isset($process_map[$v['current_node_id']]) ? $process_map[$v['current_node_id']] : '';
				$v['tasktype_name'] = isset($tasktype_list[$v['tasktype_id']]['type_name']) ? $tasktype_list[$v['tasktype_id']]['type_name'] : '';
				$v['status_name'] = isset($status_map[$v['status']]) ? $status_map[$v['status']] : '';

				$task_info[$k] = $v;
			}

			$task_info = current($task_info);
			
			$this->app->response->setBody(array(
				'progressList' => $progress_list,
				'taskInfo'     => $task_info,
			));
		}
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