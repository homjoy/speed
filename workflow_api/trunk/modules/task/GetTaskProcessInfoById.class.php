<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 任务创建
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class GetTaskProcessInfoById extends BaseModule {

	private $params;
	private $processNodeInfo;
	private $processRuleInfo = array();
	private $roleUserInfo = array();
	
	public function run() {
	
		$this->_init();
	
		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		if(empty($this->params['task_id'])) {
			$return = Response::gen_error(10002, '', 'task_id 校验为空');
			return $this->app->response->setBody($return);
		}
		
		$taskInfo = $this->getClient()->call('workflowatom', 'process/TaskList', $this->params);
		$taskInfo = $this->parseApiData($taskInfo);
		
		if($taskInfo === FALSE) {
			return;
		}
		$taskInfo = array_pop($taskInfo);
		$taskInfo['task_content'] = json_decode($taskInfo['task_content'], true);
		
		//获取task相关信息
		/*$multiClient = $this->getMultiClient();
		
		$multiClient->call('workflowatom', 'process/process_node_list', array(
			'type_id'	=> $taskInfo['tasktype_id'],
			'status'	=> 1
		), 'processInfo');
		$multiClient->call('workflowatom', 'process/progressList', array(
			'task_id'	=> $taskInfo['task_id'],
		), 'progressInfo');
		
		$multiClient->callData();
		
		$error_msg = '';
		
		if(!empty($multiClient->processInfo['content']['error_msg'])) {
			$error_msg .= $multiClient->processInfo['content']['error_msg'] . '<br />';
		}else {
			$processInfo = $this->parseApiData($multiClient->processInfo);
		}
		
		if(!empty($multiClient->progressInfo['content']['error_msg'])) {
			$error_msg .= $multiClient->progressInfo['content']['error_msg'] . '<br />';
		}else {
			$progressInfo = $this->parseApiData($multiClient->progressInfo);
		}*/
		
		$processInfo = $this->getClient()->call('workflowatom', 'process/process_node_list', array(
			'type_id'	=> $taskInfo['tasktype_id'],
			'status'	=> 1
		));
		$processInfo = $this->parseApiData($processInfo);
		$this->genProcessStruct($processInfo);
		
		$processIds = array_keys($processInfo);
		$ruleInfo = $this->getClient()->call('workflowatom', 'process/process_rule_list', array(
			'process_id' => $processIds
		));
		$ruleInfo = $this->parseApiData($ruleInfo);
		if($ruleInfo === FALSE) {
			return;
		}
		
		foreach($ruleInfo as $v) {
			$this->processRuleInfo[$v['next_process_id']] = $v;
		}
		
		$roleId = array(
			'speed'	=> array(),
			'wf'	=> array()
		);
		foreach($processInfo as $v) {
			if(!empty($v['role_id'])) {
				if(strstr($v['role_id'],'speed_')) {
					$roleId['speed'][] = str_replace('speed_', '', $v['role_id']);
				}elseif(strstr($v['role_id'],'wf_')) {
					$roleId['wf'][] = str_replace('wf_', '', $v['role_id']);
				}
			}
		}
		
		$this->roleInfo = $this->getRoleMapUserId($roleId);
		
		if(FALSE === $this->roleInfo) {
			return;
		}
		
		$processListInfo = $this->genProcessList();
		
		return $this->app->response->setBody(Response::gen_success($processListInfo));
	

	
		var_dump($processListInfo, $processInfo, $this->processRuleInfo);exit;
		
	}
	
	private function _init() {
	
		$this->rules = array(
			'task_id'	=> array(
				'type'		=> 'integer',
				'default'	=> 0,
			)
		);
		
		$this->params = $this->post()->safe();
	}
	
	private function genProcessList($prevId = 0) {
		$returnList = array();
		foreach($this->processNodeInfo[$prevId] as $n) {
			$tmp = array(
				'process_name'	=> $n['process_name'],
				//'operator'		=> array(0),
			);
			
			if(!empty($this->roleInfo[$n['role_id']])) {
				$tmp['operator'] = $this->roleInfo[$n['role_id']];
			}
			
			if(!empty($this->processRuleInfo[$n['process_id']])) {
				$tmp['rule'] = $this->processRuleInfo[$n['process_id']]['rule'];
			}
			
			if(!empty($this->processNodeInfo[$n['process_id']])) {
				$tmp['next_node'] = $this->genProcessList($n['process_id']);
			}
			
			$returnList[$n['process_id']] = $tmp;
		}
		
		return $returnList;
	}
	
	private function genProcessStruct($processInfo) {
		foreach($processInfo as $v) {
			$prevNode = explode(',', $v['pre_process_ids']);
			
			foreach($prevNode as $p) {
				$this->processNodeInfo[$p][] = $v;
			}
			
		}
	}
	
	private function getRoleMapUserId($roleId) {
		$roleUserInfo = array();
		if(!empty($roleId['speed'])) {
			
		}elseif(!empty($roleId['wf'])) {
			$wfRoleId = array_values($roleId['wf']);
			
			$mapInfo = $this->getClient()->call('workflowatom', 'user/user_role_map_list', array(
				'role_id'	=> implode(',', $wfRoleId),
				'status'	=> 1
			));
			
			$mapInfo = $this->parseApiData($mapInfo);
			if($mapInfo === FALSE) {
				return FALSE;
			}
			
			if(!empty($mapInfo)) {
				$userIds = array();
				$wfRoleInfo = array();
				
				foreach($mapInfo as $v) {
					$userIds[] = $v['user_id'];
					$wfRoleInfo[$v['role_id']][] = $v['user_id'];
				}
				
				$userIds = array_unique($userIds);
				
				$userInfo = $this->getClient()->call('atom', 'user/user_info_get', array(
					'user_id' => implode(',', $userIds)
				));
				
				$userInfo = $this->parseApiData($userInfo);
				if($userInfo === FALSE) {
					return FALSE;
				}
				
				if(!empty($wfRoleInfo)) {
					foreach($wfRoleInfo as $k => $uids) {
						foreach($uids as $u) {
							$roleUserInfo['wf_' . $k][] = array(
								'id'	=> $userInfo[$u]['user_id'],
								'name'	=> $userInfo[$u]['name_cn'],
								'email'	=> $userInfo[$u]['mail_full'],
							);
						}
					}
				}
				
			}
		}
		
		return $roleUserInfo;
	}
}