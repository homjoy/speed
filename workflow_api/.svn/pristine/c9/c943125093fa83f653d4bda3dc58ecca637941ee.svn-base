<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 任务进度列表
 * @author jingjingzhang@meilishuo.com
 * @since 2015-08-02
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class TaskProgress extends BaseModule {

	private $params;

	public function run() {

		$this->_init();

		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		if (empty($this->params['task_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}

		$progress_list = self::getClient()->call('workflowatom', 'process/progress_list', $this->params);
		$progress_list = $this->parseApiData($progress_list);

		if (empty($progress_list) && is_array($progress_list)) {
			$this->app->response->setBody(Response::gen_success($progress_list));
		} else if (!empty($progress_list)) {
			$progress_list = array_reverse($progress_list);
			$processIds = $userIds = array();
			foreach ($progress_list as $k => $v) {
				if ($v['process_id'] != 0) {
					$processIds[] = $v['process_id'];
				}
				$userIds[] = $v['current_user_id'];
			}

			$process = $process_map = array();
			if (!empty($processIds)) {
				$processIds = array_unique($processIds);
				$process = $this->getProcessInfo(array('process_id' => $processIds));
			}

			if (!empty($process)) {
				foreach ($process as $k => $v) {
					$process_map[$v['process_id']] = $v['process_name'];
				}
			}

			$user = $user_map = array();
			if (!empty($userIds)) {
				$userIds = array_unique($userIds);
				$user = $this->getUserInfo(array('user_id' => implode(',', $userIds)));
			}

			if (!empty($user)) {
				foreach ($user as $k => $v) {
					$user_map[$v['user_id']] = $v['name_cn'];
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

			$result = array();
			foreach ($progress_list as $k => $v) {

				if ($v['current_user_id'] != 0 && isset($user_map[$v['current_user_id']])) {
					$v['current_user_name'] = $user_map[$v['current_user_id']];
				}

				if ($v['process_id'] != 0 && isset($process_map[$v['process_id']])) {
					$v['process_name'] = $process_map[$v['process_id']];
				} else {
					$v['process_name'] = '创建任务';
				}
				$v['status_name'] = $status_map[$v['status']];

				$result[$v['task_id']][] = $v;
			}

			$this->app->response->setBody(Response::gen_success($result));
		}
	}

	private function getProcessInfo($param) {

		$ret = self::getClient()->call('workflowatom', 'process/process_node_list', $param);
		$ret = $this->parseApiData($ret);

		return $ret;
	}

	private function getUserInfo($param) {

		$ret = self::getClient()->call('atom', 'user/user_info_get', $param);
		$ret = $this->parseApiData($ret);

		return $ret;
	}

	private function _init() {

		$this->rules = array(
			'task_id' => array(
				'required'   => TRUE,
				'type'       => 'integer',
			),
		);

		$this->params = $this->post()->safe();
	}
}