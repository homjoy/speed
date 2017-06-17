<?php 
namespace Admin\Modules\Workflow\Task;

/**
 * 任务管理
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-22
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class Manage extends BaseModule {

	protected $params = NULL;
	protected $errors = NULL;
	protected $page_size = 10;

	protected $checkUserPermission = TRUE;

	public function run() {

		$this->_init();

		if ($this->query()->hasError()) {
			$return = Response::gen_error(10001, '', $this->errors);
			return $this->app->response->setBody($return);
		}

		$queryParams = array();

		// 分页控制
		if (isset($this->params['page'])) {
			if ($this->params['page'] <= 0) {
				$queryParams['page'] = 1;
			} else {
				$queryParams['page'] = $this->params['page'];
			}

			$queryParams['limit'] = $this->page_size;
			$queryParams['page_flag'] = 1;
		}

		$multiClient = self::getMultiClient();
		$multiClient->call('workflowatom', 'process/task_type_list', array(), 'task_type_list');
		$multiClient->call('workflowatom', 'process/task_list', $queryParams, 'task_list');
		$multiClient->call('workflowatom', 'process/task_list', array('count' => 1), 'count');
		$multiClient->callData();
		$task_type_list = $this->parseApiData($multiClient->task_type_list);
		$task_list = $this->parseApiData($multiClient->task_list);
		$count = $this->parseApiData($multiClient->count);

		$parent_type = $task_type_map = $sub_type = $user_ids = $user_map = array();
		if (!empty($task_type_list)) {
			foreach ($task_type_list as $k => $v) {
				$task_type_map[$v['type_id']] = $v['type_name'];
				if ($v['type_parent_id'] == 0) {
					$parent_type[] = $v;
				}
			}
		}
		
		if (!empty($parent_type)) {
			foreach ($parent_type as $k_parent => $v_parent) {
				foreach ($task_type_list as $k_list => $v_list) {
					if ($v_list['type_parent_id'] == $v_parent['type_id']) {
						$sub_type[$v_parent['type_id']][] = $v_list;
					}
				}
			}
		}

		if (!empty($task_list)) {
			foreach ($task_list as $k => $v) {
				$user_ids[] = $v['user_id'];
				$user_ids[] = $v['current_user_id'];
			}
		}

		$user_info = self::getClient()->call('atom', 'account/get_user_info', array('user_id' => implode(',', $user_ids), 'status' => array(1,2,3), 'all' => 1));
		$user_info = $this->parseApiData($user_info);

		if (!empty($user_info)) {
			foreach ($user_info as $k => $v) {
				$user_map[$v['user_id']] = $v['name_cn']; 
			}
		}

		if (!empty($task_list)) {
			foreach ($task_list as $k => $v) {
				$v['tasktype_name'] = isset($task_type_map[$v['tasktype_id']]) ? $task_type_map[$v['tasktype_id']] : '';
				$v['user_name'] = isset($user_map[$v['user_id']]) ? $user_map[$v['user_id']] : '';
				
				if ($v['current_user_id'] != 0) {
					$v['current_user_name'] = isset($user_map[$v['current_user_id']]) ? $user_map[$v['current_user_id']] : '';
				} else {
					$v['current_user_name'] = '未开始审核';
				}

				$task_list[$k] = $v;
			}
		}

		$return['parentType'] = $parent_type;
		$return['subType'] = $sub_type;
		$return['taskList'] = !empty($task_list) ? $task_list : array();
		$return['count'] = !empty($count) ? ceil($count/$this->page_size) : 0;
		$return['page'] = $this->params['page'];

		$this->app->response->setBody($return);
	}

	private function _init() {

		$this->rules = array(
            'page'  => array(
                'type'     => 'integer',
                'default'  => 1,
            ),
		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
}