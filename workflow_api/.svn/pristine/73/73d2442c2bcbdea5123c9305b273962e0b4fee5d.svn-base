<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 任务进度列表
 * @author yixiangwang@meilishuo.com
 * @since 2015-09-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;
use WorkFlowApi\Package\Task\TaskTransfer;

class GetProcessList extends BaseModule {

	private $params;
	private $processInfo;
	private $taskInfo = array();
	private $reportLine;
	private $ruleInfo;

	public function run() {
		//$start = microtime(true);
		$this->_init();
		
		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		if (empty($this->params['task_id']) && empty($this->params['tasktype_id'])) {
			$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
		}
		
		if( !empty($this->params['task_id']) ) {
			//获取task信息
			$taskInfo = $this->getClient()->call('workflowatom', 'process/TaskList', $this->params);
			$taskInfo = $this->parseApiData($taskInfo);
			
			if($taskInfo === FALSE) {
				return;
			}
			$this->taskInfo = $taskInfo = array_pop($taskInfo);
		}
		
		$tasktype_id = 0;
		if( !empty($this->taskInfo['tasktype_id']) ) {
			$tasktype_id = $this->taskInfo['tasktype_id'];
		}elseif( !empty($this->params['tasktype_id']) ) {
			$tasktype_id = $this->params['tasktype_id'];
		}
		
		$multiClient = $this->getMultiClient();
		//获取process info
		$multiClient->call('workflowatom', 'process/process_node_list', array(
			'type_id'	=> $tasktype_id,
			'status'		=> 1
		), 'processInfo');
	
		if( !empty($this->taskInfo) ) {
			//获取progress log
			$multiClient->call('workflowatom', 'process/progress_list', array(
				'task_id'	=> $this->params['task_id'],
			), 'progress_list');
		}
		
		//获取汇报线
		if( !empty($this->params['depart_id']) ) {
			$queryParams = array(
				'depart_id'	=> $this->params['depart_id']
			);
		}else {
			$queryParams = array(
				'user_id' => empty($taskInfo['user_id']) ? $this->params['user_id'] : $taskInfo['user_id']
			);
		}
		$multiClient->call('atom', 'department/get_all_depart_leader', $queryParams, 'reportLine');
		$multiClient->callData();
		$error_msg = '';
		if(!empty($multiClient->processInfo['content']['error_msg'])) {
			$error_msg .= 'processInfo: ' . $multiClient->processInfo['content']['error_msg'] . '<br />';
		}
		
		if(!empty($multiClient->progress_list['content']['error_msg'])) {
			$error_msg .= 'progress_list: ' . $multiClient->progress_list['content']['error_msg'] . '<br />';
		}
		
		if(!empty($multiClient->reportLine['content']['error_msg'])) {
			$error_msg .= 'reportLine: ' . $multiClient->reportLine['content']['error_msg'] . '<br />';
		}
		
		if( !empty($error_msg) ) {
			return $this->app->response->setBody(Response::gen_error(10000, $error_msg));
		}
		
		//获取流程数据
		$this->processInfo = $multiClient->processInfo['content']['data'];

		//获取处理历史记录
		$progress_list = empty($multiClient->progress_list['content']['data']) ? array() : $multiClient->progress_list['content']['data'];
		$progressNodeIds = array();
		if (!empty($progress_list)) {
			foreach ($progress_list as $k => $v) {
				if ($v['process_id'] != 0) {
					$progressNodeIds[] = $v['process_id'];
				}
			}
		}
		$progressNodeIds = array_unique($progressNodeIds);

		//读取规则
		$pids = array();
		if (!empty($this->processInfo)) {
			$pids = array_keys($this->processInfo);
		}
		
		$ruleInfo = $this->getClient()->call('workflowatom', 'process/process_rule_list', array(
			'process_id' => $pids
		));
		$ruleInfo = $this->parseApiData($ruleInfo);
		if(FALSE === $ruleInfo) {
			return FALSE;
		}
		if(!empty($ruleInfo)) {
			foreach($ruleInfo as $v) {
				$this->ruleInfo[$v['process_id']][] = $v;
			}
		}

		$currentNodeId = array();
		if (!empty($taskInfo['current_node_id'])) {
			$currentNodeId[] = $taskInfo['current_node_id'];
		}
		
		//格式化流程信息
		$firstNode = TaskTransfer::getEndNode($this->processInfo);
		$processNode = $this->genProcessLine($firstNode['process_id'], $progressNodeIds, $currentNodeId);
		if(FALSE === $processNode) {
			return;
		}
		
		$lastNodeId = array_keys($processNode);
		$lastNodeId = array_pop($lastNodeId);
		
		//获取汇报线
		$this->reportLine = $multiClient->reportLine['content']['data'];
		
		//格式化历史数据格式
		$progressLogInfo = array();
		if(!empty($progress_list)) {
			foreach($progress_list as $v) {
				if( !empty($v['process_id']) ) {
					$progressLogInfo['pid_' . $v['process_id']][] = $v;
				}
			}
		}
		
		$resultInfo = array(
			'current_node_id'	=> empty($taskInfo['current_node_id']) ? 0 : $taskInfo['current_node_id'],
			'last_node_id'		=> $lastNodeId,
			'process_node_info'	=> $processNode,
			//'progress_info'		=> array(),
			'stop'				=> 0
		);
		$userIds = array();
		//$progressData = array();
	
		//生成已经处理的相关信息
		if( !empty($progressLogInfo) ) {
			foreach($processNode as $pid => $v) {
				if( !empty($progressLogInfo[$pid]) ) {
					foreach($progressLogInfo[$pid] as $log) {
						if( !empty($log['process_id']) && !empty($log['current_user_id']) ) {
							$resultInfo['process_node_info'][$pid]['progress_info']['uid_' . $log['current_user_id']] = array(
								'user_id'			=> $log['current_user_id'],
								'progress'			=> $log
							);
							
							if(4 == $log['status'] || 5 == $log['status']) {
								$resultInfo['process_node_info'][$pid]['progress_info']['uid_' . $log['current_user_id']]['is_end'] = 1;
								$resultInfo['stop']	= 1;
							}
							
							$userIds[] = $log['current_user_id'];
						}
					}
				}else {
					break;
				}
			}
		}
	
		//分类节点role_id形式
		$nodeType = array(
			'speed'			=> array(),
			'wf'			=> array(),
			'speed_till'	=> array(),
			'direct'        => array(),
			'speed_before'  => array(),
			'speed_after'   => array(),
		);
		$currentRoleId = 0;
		foreach($this->reportLine as $v) {
			if(isset($this->taskInfo['current_user_id']) && $v['leader_user_id'] == $this->taskInfo['current_user_id']) {
				$currentRoleId = $v['role_id'];
			}
		}
		
		foreach($processNode as $k => $v) {
			$pid = str_replace('pid_', '', $k);
			
			if( !empty($this->processInfo[$pid]['role_id']) ) {
				if(!empty(strstr($this->processInfo[$pid]['role_id'], 'wf_'))) {
					$nodeType['wf'][] = str_replace('wf_', '', $this->processInfo[$pid]['role_id']);
				}elseif(!empty(strstr($this->processInfo[$pid]['role_id'], 'direct_'))){
					$nodeType['direct'][] = str_replace('direct_', '', $this->processInfo[$pid]['role_id']);
				}elseif(!empty(strstr($this->processInfo[$pid]['role_id'], 'speed_')) && !empty(strstr($this->processInfo[$pid]['role_id'], '_till'))) {
					$nodeType['speed_till'][] = str_replace('_till', '', str_replace('speed_', '', $this->processInfo[$pid]['role_id']) );
				}elseif(!empty(strstr($this->processInfo[$pid]['role_id'], 'speed_')) && !empty(strstr($this->processInfo[$pid]['role_id'], '_before'))) {
					$nodeType['speed_before'][] = str_replace('_before', '', str_replace('speed_', '', $this->processInfo[$pid]['role_id']) );
				}elseif(!empty(strstr($this->processInfo[$pid]['role_id'], 'speed_')) && !empty(strstr($this->processInfo[$pid]['role_id'], '_after'))) {
					$nodeType['speed_after'][] = str_replace('_after', '', str_replace('speed_', '', $this->processInfo[$pid]['role_id']) );
				}else {
					$nodeType['speed'][] = str_replace('speed_', '', $this->processInfo[$pid]['role_id']);
				}
			}
			
		}
		
		//获取Group模式节点，权限人id
		if( !empty($nodeType['wf']) ) {
			
			$ruleMapList = $this->getClient()->call('workflowatom', 'user/userRoleMapList', array(
				'role_id'		=> implode(',', $nodeType['wf']),
				'status'		=> 1
			));
			$ruleMapList = $this->parseApiData($ruleMapList);
			
			if(FALSE === $ruleMapList) {
				return FALSE;
			}
			
			$roleInfo = array();
			foreach($ruleMapList as $v) {
				$roleInfo[$v['role_id']][] = $v['user_id'];
				$userIds[] = $v['user_id'];
			}
		}
		
		//获取用户信息
		$userInfo = array();
		if (!empty($userIds)) {
			$userIds = array_unique($userIds);
			$userInfo = $this->getClient()->call('atom', 'account/get_user_info', array(
				'user_id' => implode(',', $userIds),
				'all' => 1,
				'status' => array(1,2,3)
			));
			$userInfo = $this->parseApiData($userInfo);
			
			if(FALSE === $userInfo) {
				return FALSE;
			}
		}

		//清除已完成的部分
		if( !empty($taskInfo['current_node_id']) ) {
			foreach($processNode as $k => $v) {
				$pid = str_replace('pid_', '', $k);
				if($taskInfo['current_node_id'] == $pid) {
					break;
				}
				array_shift($processNode);
			}
		}
		
		if( empty($this->taskInfo) || ( !empty($this->taskInfo) && !in_array($this->taskInfo['status'], array(4,5,6)) ) ) {
			//生成后续处理人员信息
			foreach($processNode as $pidKey => $v) {
				
				$pid = str_replace('pid_', '', $pidKey);
				$role_id = $this->processInfo[$pid]['role_id'];
			
				if(empty($role_id)) {
					continue;
				}
				
				if(!empty(strstr($role_id, 'wf_'))) {
					if(empty($progressLogInfo[$pidKey])) {
						$role_id = str_replace('wf_', '', $role_id);
					
						foreach($roleInfo[$role_id] as $uid) {
							if( empty($resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $uid]) ) {
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $uid] = array(
									'user_id'		=> $uid,
									'user_name'		=> $userInfo[$uid]['name_cn']
								);
								
								if($lastNodeId == $pidKey) {
									$resultInfo['process_node_info'][$pidKey]['is_end'] = 1;
								}
							}
						}
					}
					
				}else if (!empty(strstr($role_id, 'speed_'))){
					$role_id = str_replace('speed_', '', $role_id);
					
					//特定职位审批
					if(!empty(strstr($role_id, '_till'))) {
						$role_id = str_replace('_till', '', $role_id);
						
						foreach($this->reportLine as $v) {
							if( $role_id <= $v['role_id'] && empty($resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]) ) {
								if( !empty($currentRoleId) && $v['role_id'] > $currentRoleId) {
									continue;
								}
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']] = array(
									'user_id'		=> $v['leader_user_id'],
									'user_name'		=> $v['leader_user_name']
								);
								
								if($role_id == $v['role_id'] && $lastNodeId == $pidKey && empty($resultInfo['stop'])) {
									$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]['is_end'] = 1;
								}
							}
						}
						
					} else if (!empty(strstr($role_id, '_before'))) {
						$role_id = str_replace('_before', '', $role_id);
	
						foreach ($this->reportLine as $k => $v) {
							if( $role_id < $v['role_id'] && empty($resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]) ) {
								if( !empty($currentRoleId) && $v['role_id'] > $currentRoleId) {
									continue;
								}
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']] = array(
									'user_id'		=> $v['leader_user_id'],
									'user_name'		=> $v['leader_user_name']
								);
								
								if($role_id == $this->reportLine[$k + 1]['role_id'] && $lastNodeId == $pidKey && empty($resultInfo['stop'])) {
									$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]['is_end'] = 1;
								}
							}
						}
	
					} else if (!empty(strstr($role_id, '_after'))) {
						$role_id = str_replace('_after', '', $role_id);
	
						foreach ($this->reportLine as $k => $v) {
							if( $role_id <= $v['role_id'] && empty($resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]) ) {
								if( !empty($currentRoleId) && $v['role_id'] > $currentRoleId) {
									continue;
								}
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']] = array(
									'user_id'		=> $v['leader_user_id'],
									'user_name'		=> $v['leader_user_name']
								);
	
								if ($role_id == $v['role_id'] || (($role_id < $v['role_id']) && $role_id > $this->reportLine[$k+1]['role_id'])) {
	
									if (isset($this->reportLine[$k+1])) {
										$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $this->reportLine[$k+1]['leader_user_id']] = array(
											'user_id'		=> $this->reportLine[$k+1]['leader_user_id'],
											'user_name'		=> $this->reportLine[$k+1]['leader_user_name']
										);
									}
									
									if ($lastNodeId == $pidKey && empty($resultInfo['stop'])) {
										$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]['is_end'] = 1;
									}
								}
							}
						}
					} else {
						foreach($this->reportLine as $v) {
							if($role_id == $v['role_id']) {
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']] = array(
									'user_id'		=> $v['leader_user_id'],
									'user_name'		=> $v['leader_user_name']
								);
								
								if($lastNodeId == $pidKey) {
									$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $v['leader_user_id']]['is_end'] = 1;
								}
							}
						}
					}
				} else {
	
					if (empty($progressLogInfo[$pidKey])) {
						$role_id = str_replace('direct_', '', $role_id);
	
						for ($i = 1; $i <= $role_id; $i++) {
							$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $this->reportLine[$i-1]['leader_user_id']] = array(
								'user_id'		=> $this->reportLine[$i-1]['leader_user_id'],
								'user_name'		=> $this->reportLine[$i-1]['leader_user_name']
							);
	
							if($lastNodeId == $pidKey) {
								$resultInfo['process_node_info'][$pidKey]['progress_info']['uid_' . $this->reportLine[$i-1]['leader_user_id']]['is_end'] = 1;
							}
						}
					}
				}
			}
		}
		
		foreach($resultInfo['process_node_info'] as $nodeKey => $node) {
			if(!empty($node['progress_info'])) {
				foreach($node['progress_info'] as $userKey => $v) {
					if(empty($v['user_name'])) {
						$resultInfo['process_node_info'][$nodeKey]['progress_info'][$userKey]['user_name'] = $userInfo[$v['user_id']]['name_cn'];
					}
				}
			}else {
				unset($resultInfo['process_node_info'][$nodeKey]);
			}
		}
		
		//print_r($resultInfo);
		$this->app->response->setBody(Response::gen_success($resultInfo));
		//echo microtime(true) - $start;
	}
	
	private function genProcessLine($pid, $progressNodeIds, $currentNodeId) {
		$return = array();
		
		//if( !empty($this->processInfo[$pid]['pre_process_ids']) ) {
		$return['pid_' . $this->processInfo[$pid]['process_id']] = array(
			'name'	=> $this->processInfo[$pid]['process_name']
		);
		
		if ( strstr($this->processInfo[$pid]['role_id'], 'wf_') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'group';
		} else if ( strstr($this->processInfo[$pid]['role_id'], '_till') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'cascading';
		} else if ( strstr($this->processInfo[$pid]['role_id'], '_before') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'cascading';
		} else if ( strstr($this->processInfo[$pid]['role_id'], '_after') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'cascading';
		} else if ( strstr($this->processInfo[$pid]['role_id'], 'speed_') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'position';
		} else if ( strstr($this->processInfo[$pid]['role_id'], 'direct_') !== FALSE ) {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'direct';
		} else {
			$return['pid_' . $this->processInfo[$pid]['process_id']]['type'] = 'persion';
		}
		//}
		
		if( '0' === $this->processInfo[$pid]['next_process_ids'] ) {
			return $return;
		}
		
		$next_node_id = explode(',', $this->processInfo[$pid]['next_process_ids']);
		
		if(count($next_node_id) == 1) {
			
			$next_node_id = array_pop($next_node_id);
			$nextNodeInfo = $this->genProcessLine($next_node_id, $progressNodeIds, $currentNodeId);
			
		}elseif(count($next_node_id) > 1) {
			
			
			if( !empty($this->taskInfo['task_content']) ) {
				$ruleParams = json_decode(html_entity_decode($this->taskInfo['task_content']), true);
			}else {
				$ruleParams = array();
			}
			
			if(!isset($this->params['ruleExtendParams'])) {
				$this->params['ruleExtendParams'] = array();
			}
			
			if(!empty($this->params['ruleExtendParams']) && is_array($this->params['ruleExtendParams'])) {
				$ruleParams = array_merge($ruleParams, $this->params['ruleExtendParams']);
			}
			
			//无默认分支
			/*foreach($this->ruleInfo[$pid] as $r) {
				
				if(! TaskTransfer::getRuleResult($r['rule'], $ruleParams)) {
					continue;
				}
				
				$nid = $r['next_process_id'];
				break;
				
			}*/
			
			$ruleProgressNodeInter = array_intersect($next_node_id, $progressNodeIds);
			$ruleCurrentNodeInter = array_intersect($next_node_id, $currentNodeId);
			if (!empty($ruleProgressNodeInter)) {
				$nid = current($ruleProgressNodeInter);
			}else if ($ruleCurrentNodeInter) {
				$nid = current($ruleCurrentNodeInter);
			} else {
				//有默认分支
				foreach($this->ruleInfo[$pid] as $r) {
					
					$nid = $r['next_process_id'];
					
					if( TaskTransfer::getRuleResult($r['rule'], $ruleParams) ) {
						break;
					}
				}
			}
		
			if( !empty($nid) ) {
				$nextNodeInfo = $this->genProcessLine($nid, $progressNodeIds, $currentNodeId);
			}else {
				$nextNodeInfo = array();
			}
			
		}else {
			$this->app->response->setBody(Response::gen_error(10002, '', '生成参数异常'));
			return FALSE;
		}
		
		$return = array_merge($return, $nextNodeInfo);
		
		return $return;
	}

	private function _init() {

		$this->rules = array(
			'task_id' => array(
				'type'       => 'integer',
			),
			'depart_id' => array(
				'type'       => 'integer',
			),
			'tasktype_id' => array(
				'type'       => 'integer',
			),
			'user_id' => array(
				'type'       => 'integer',
			),
		);

		$this->params = $this->post()->safe();
	
		if( isset($this->request->POST['ruleExtendParams']) ) {
			$this->params['ruleExtendParams'] = $this->request->POST['ruleExtendParams'];
		}
	}
}