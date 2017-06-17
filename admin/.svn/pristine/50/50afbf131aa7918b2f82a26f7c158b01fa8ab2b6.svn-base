<?php
namespace Admin\Modules\Workflow\Process;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\TaskType;
use Libs\Util\Format;

class AjaxPorcessRuleEdit extends BaseModule {
	
	private $params = array();
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		if(!$this->_init()) {
			return FALSE;
		}
		//print_r($this->params['rule']);exit;
		$nodeData = $this->getClient()->call('workflowatom', 'process/get_process_info_by_id', array(
			'process_id'	=> $this->params['process_id']
		));
		$nodeData = $this->parseApiData($nodeData);
		
		$prevNode = array_unique($this->params['prev_node'], SORT_NUMERIC);
		$nextNode = array_unique($this->params['next_node'], SORT_NUMERIC);
		
		sort($prevNode, SORT_NUMERIC);
		sort($nextNode, SORT_NUMERIC);
		//var_dump($nodeData['pre_process_ids'] , $prevNode , $nodeData['next_process_ids'] , $nextNode);exit;
		if( $nodeData['pre_process_ids'] != implode(',', $prevNode) || $nodeData['next_process_ids'] != implode(',', $nextNode) ) {
			$updateNodeParams = array(
				'process_id'		=> $this->params['process_id'],
				'pre_process_ids'	=> implode(',', $prevNode),
				'next_process_ids'	=> implode(',', $nextNode),
			);
			
			$nodeUpdate = $this->getClient()->call('workflowatom', 'process/process_node_edit', $updateNodeParams);
			$nodeUpdate = $this->parseApiData($nodeUpdate);
			
			if($nodeUpdate === FALSE) {
				return;
			}
		}
		
		
		if(count($nextNode) > 1) {
			$ruleData = $this->getClient()->call('workflowatom', 'process/process_rule_list', array(
				'process_id'	=> $this->params['process_id']
			));
			
			$ruleData = $this->parseApiData($ruleData);
			if($ruleData === FALSE) {
				return;
			}
			
			$createRule = array();
			$updateRule = array();
			
			foreach($this->params['rid'] as $k => $v) {
				
				if(empty($v)) {
					//创建
					if(empty($createRule[$this->params['next_node'][$k]]) && !empty($this->params['rule'][$k])) {
						$createRule[$this->params['next_node'][$k]] = array(
							'process_id'		=> $this->params['process_id'],
							'next_process_id'	=> $this->params['next_node'][$k],
							'rule'				=> $this->params['rule'][$k]
						);
					}
				}else {
					//更新
					
					if( $ruleData[$v]['next_process_id'] != $this->params['next_node'][$k] || $ruleData[$v]['rule'] != $this->params['rule'][$k] ) {
						$updateRule[] = array(
							'id'				=> $v,
							'next_process_id'	=> $this->params['next_node'][$k],
							'rule'				=> $this->params['rule'][$k]
						);
					}
					
					unset($ruleData[$v]);
				}
			}
			
			$ruleDelete = array_keys($ruleData);
			
			//var_dump($createRule, $updateRule, $ruleDelete);exit;
			$multiClient = $this->getMultiClient();
			
			if(!empty($createRule)) {
				$multiClient->call('workflowatom', 'process/process_rule_create', array(
					'data'	=> $createRule
				), 'createRule');
			}
			
			if(!empty($updateRule)) {
				$multiClient->call('workflowatom', 'process/process_rule_edit', array(
					'data'	=> $updateRule
				), 'updateRule');
			}
			
			if(!empty($ruleDelete)) {
				$multiClient->call('workflowatom', 'process/process_rule_delete', array(
					'rule_id'	=> implode(',', $ruleDelete)
				), 'ruleDelete');
			}
			
			$multiClient->callData();
			
			$error_msg = '';
			//var_dump($multiClient->createRule['content']['data']);exit;
			if(!empty($multiClient->createRule['content']['error_msg'])) {
				$error_msg .= $multiClient->createRule['content']['error_msg'] . '<br />';
			}
			
			if(!empty($multiClient->updateRule['content']['error_msg'])) {
				$error_msg .= $multiClient->updateRule['content']['error_msg'] . '<br />';
			}
			
			if(!empty($multiClient->ruleDelete['content']['error_msg'])) {
				$error_msg .= $multiClient->ruleDelete['content']['error_msg'] . '<br />';
			}
			//var_dump($multiClient->createRule, $error_msg);exit;
			if(!empty($error_msg)) {
				$this->app->response->setBody(Response::gen_error(10000, $error_msg));
				return;
			}
		}
		
		$this->app->response->setBody(Response::gen_success('保存成功'));
	}
	
	protected function _init() {
		
		$this->params['process_id'] = isset($this->request->POST['process_id']) ? $this->request->POST['process_id'] : 0;
		//上级节点
		$this->params['prev_node'] = isset($this->request->POST['prev_node']) ? $this->request->POST['prev_node'] : array();
		//下级节点
		$this->params['next_node'] = isset($this->request->POST['next_node']) ? $this->request->POST['next_node'] : array();
		//规则
		
		if(empty($this->params['process_id']) || empty($this->params['prev_node']) || empty($this->params['next_node'])) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		/*if(count($this->params['prev_node']) > 1) {
			$this->params['prev_node'] = array_unique($this->params['prev_node'], SORT_NUMERIC);
		}
		
		if(count($this->params['next_node']) > 1) {
			$this->params['next_node'] = array_unique($this->params['next_node'], SORT_NUMERIC);
		}*/
		
		if(count($this->params['next_node']) > 1) {
			//校验规则参数
			$this->params['rid'] = isset($this->request->POST['rid']) ? $this->request->POST['rid'] : array();
			$this->params['rule'] = isset($this->request->POST['rule']) ? $this->request->POST['rule'] : array();
			$ridCount = count($this->params['rid']);
			$ruleCount = count($this->params['rule']);
			$next_nodeCount = count($this->params['next_node']);
			
			if($next_nodeCount != $ridCount || $next_nodeCount != $ruleCount) {
				$return = Response::gen_error(10001, '规则数不匹配');
				$this->app->response->setBody($return);
				
				return FALSE;
			}
			
			foreach($this->params['rule'] as $k => $v) {
				$this->params['rule'][$k] = htmlspecialchars_decode( htmlspecialchars_decode($v) );
			}
		}
		
		return TRUE;
	}

}