<?php
namespace WorkFlowApi\Modules\Task;

/**
 * 任务搜索
 * @author jingjingzhang@meilishuo.com
 * @since 2015-12-29
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;
use Libs\Util\ArrayUtilities;

class SearchTask extends BaseModule {

	private $params;
	private $errors;

	public function run() {

		$this->_init();

		//参数校验
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}

		$queryParams = $query_task_ids = $task_map = array();
		foreach ($this->params as $k => $v) {
			if ($k == 'start_update_time' && !empty($v)) {
				$queryParams['start_time'] = $this->params['start_update_time'];
				$countParams['start_time'] = $this->params['start_update_time'];
			} else if ($k == 'end_update_time' && !empty($v)) {
				$queryParams['end_time'] = $this->params['end_update_time'];
			} else if ($k != 'approve_user_id' && $k != 'page' && $k != 'limit' && $k != 'order' && $k != 'page_flag' &&  $k != 'count' && !empty($v)) {
				$queryParams[$k] = $v;
			}
		}

		$multiClient = self::getMultiClient();
		$multiResultClient = self::getMultiClient();

		// 搜索审批人审批数据
		if (!empty($this->params['approve_user_id'])) {

			if (!empty($queryParams)) {
				$multiClient->call('workflowatom', 'process/task_list', $queryParams, 'task_list');
			}

			$multiClient->call('workflowatom', 'user/user_role_map_list', array( //获取审批人具有的所有角色
	        	'user_id' => $this->params['approve_user_id'],
	        ), 'my_role_list');

	        $multiClient->call('workflowatom', 'process/process_node_list', array( //获取当前任务类型的所有节点
				'type_id' => $this->params['tasktype_id'],
				'status'  => 1,
			), 'process_list');

			$multiClient->call('workflowatom', 'process/progress_list', array( 
				'current_user_id' => $this->params['approve_user_id'], 
				'action_type'     => array(1, 2),
			), 'progress_list_person');

			$multiClient->callData();

			$my_role_list = $this->parseApiData($multiClient->my_role_list);
	        $process_list = $this->parseApiData($multiClient->process_list);
	        $progress_list_person = $this->parseApiData($multiClient->progress_list_person);

	        $task_list = $task_ids = array();
	        if ($multiClient->task_list) {
	        	$task_list = $this->parseApiData($multiClient->task_list);
	        }

	        if (!empty($task_list)) {
	        	$task_ids = ArrayUtilities::my_array_column($task_list, 'task_id');
	        }

	        $roleIds = $processIds = $allProcessIds = array();

	        // 获取审批人具有的角色
	        if (!empty($my_role_list)) {
	        	$roleIds = ArrayUtilities::my_array_column($my_role_list, 'role_id');
	        }

	        // 获取审批人具有审批权限的节点
	        if (!empty($process_list)) {
	        	foreach ($process_list as $k => $v) {
	        		$allProcessIds[] = $v['process_id'];
	        		if (!empty($roleIds) && strstr($v['role_id'], 'wf_')) {
	        			$role_id = str_replace('wf_', '', $v['role_id']);
	        			if (in_array($role_id, $roleIds)) {
	        				$processIds[] = $v['process_id'];
	        			}
	        		} 
	        	}
	        }

	        $progress_list_group = array();
			if (!empty($processIds)) {
				$progress_list_group = self::getClient()->call('workflowatom', 'process/progress_list', array(
					'process_id'  => $processIds,
					'action_type' => array(1, 2),
				)); 

				$progress_list_group = $this->parseApiData($progress_list_group);
			}

			if (!empty($progress_list_group)) {
				foreach ($progress_list_group as $k => $v) {
					if ($v['process_id'] != 0) {
						$query_task_ids[] = $v['task_id'];
						$task_map[$v['task_id']] = $v['action_type'];
					}		
				}
			}

			if (!empty($progress_list_person)) {		
				foreach ($progress_list_person as $k => $v) {
					if ($v['process_id'] != 0 && in_array($v['process_id'], $allProcessIds)) {
						$query_task_ids[] = $v['task_id'];
						$task_map[$v['task_id']] = $v['action_type'];
					}		
				}
			}

			if (!empty($query_task_ids)) {

				$query_task_ids = array_unique($query_task_ids);

				if (!empty($task_ids)) {
					$query_task_ids = array_intersect($task_ids, $query_task_ids);
				} else {
					$query_task_ids = $query_task_ids;
				}

				if (!empty($this->params['count'])) {
					$count = count($query_task_ids);
				}

				if (!empty($this->params['order'])) {

					$queryParams['order'] = $this->params['order'];

					if (isset($this->params['order']['id']) && !empty($this->params['order']['id'])) {
						if (in_array($this->params['order']['id'], array('ASC', 'asc'))) {
							asort($query_task_ids);
						} else if (in_array($this->params['order']['id'], array('DESC', 'desc'))) {
							arsort($query_task_ids);
						}
					} else if (isset($this->params['order']['create_time']) && !empty($this->params['order']['create_time'])) {
						if (in_array($this->params['order']['create_time'], array('ASC', 'asc'))) {
							asort($query_task_ids);
						} else if (in_array($this->params['order']['create_time'], array('DESC', 'desc'))) {
							arsort($query_task_ids);
						}
					}
				}

				if (!empty($this->params['page_flag'])) {
					$offset = ($this->params['page'] - 1) * $this->params['limit'];
					$queryParams['task_id'] = array_slice($query_task_ids, $offset, $this->params['limit']);
				} else {
					$queryParams['task_id'] = $query_task_ids;
				}

				if (!empty($queryParams['task_id'])) {
					$multiResultClient->call('workflowatom', 'process/task_list', $queryParams, 'result');
					$multiResultClient->callData();
				} else {
					$result = array();
				}
			}
		} else {
			if (!empty($this->params['count'])) {
				$countParams = $queryParams;
				$countParams['count'] = 1;

				$multiResultClient->call('workflowatom', 'process/task_list', $countParams, 'count');
			}

			if (!empty($this->params['page_flag'])) {
				$queryParams['page_flag'] = $this->params['page_flag'];
				$queryParams['page'] = $this->params['page'];
				$queryParams['limit'] = $this->params['limit'];
			}

			if (!empty($this->params['order'])) {
				$queryParams['order'] = $this->params['order'];
			}

			$multiResultClient->call('workflowatom', 'process/task_list', $queryParams, 'result');
			$multiResultClient->callData();
		}

		if ($multiResultClient->count) {
			$count = $this->parseApiData($multiResultClient->count);
		}

		if ($multiResultClient->result) {
			$result = $this->parseApiData($multiResultClient->result);
		}

		if (empty($result) && is_array($result)) {

			$this->app->response->setBody(Response::gen_success($result));
		} else if (!empty($result)) {

			if (!empty($this->params['approve_user_id'])) {
				foreach ($result as $k => $v) {
					$v['action_type'] = isset($task_map[$v['task_id']]) ? $task_map[$v['task_id']] : '';
					$result[$k] = $v;
				}
			}
			
			if (!empty($this->params['count'])) {
				$result['count'] = $count;
			}
			
			$this->app->response->setBody(Response::gen_success($result));
		}	
	}

	private function _init() {

		$this->rules = array(
			'task_id' => array( 
				'type'      => 'multiId',
			),
			'user_id' => array( //创建人ID
				'type'      => 'integer',
			),
			'tasktype_id' => array(
				'type'      => 'integer',
			),
			'task_name' => array(
				'type' => 'string',
			),
			'start_create_time' => array( //创建开始时间
				'type' => 'datetime',
			),
			'end_create_time' => array( //创建结束时间
				'type' => 'datetime',
			),
			'start_update_time' => array( //更新开始时间
				'type' => 'datetime',
			),
			'end_update_time' => array( //更新结束时间
				'type' => 'datetime',
			),
			'approve_user_id' => array( //审批人
				'type'       => 'integer',
				'maxLength'  => 11,
			),
			'current_user_id' => array( //当前审批人
				'type'       => 'integer',
			),
			'status' => array( //状态
				'type' => 'multiId',
			),
			'current_node_id' => array( //当前审批节点
				'type' => 'integer',
			),
			'page' => array( 
				'type'    => 'integer',
				'default' => 1,
			),
			'limit' => array( 
				'type'    => 'integer',
				'default' => 100,
			),
			'order' => array( //排序，支持id和任务创建时间排序
				'type' => 'string',
			),
			'page_flag' => array( //标记是否分页：1为分页
				'type'    => 'integer',
			),
			'count' => array( //返回获取数据数量
				'type'    => 'integer',
			),
		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}