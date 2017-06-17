<?php
namespace Admin\Modules\Workflow\Process;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\TaskType;
use Admin\Package\Workflow\Process;

class ProcessInfo extends BaseModule {
	
	private $params;
	private $actionInfo;

	public function run() {
		
		$this->_init();
		
		//参数校验
        if($this->query()->hasError()){
        	$return = Response::gen_error(10001, '', $this->query()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		if(empty($this->params['type_id'])) {
			$return = Response::gen_error(10002, '', 'type_id 数据有误！');
			return $this->app->response->setBody($return);
		}
		
		$multiClient = $this->getMultiClient();
		
		$multiClient->call('workflowatom', 'process/get_type_info_by_id', $this->params, 'typeInfo');
        $multiClient->call('workflowatom', 'process/process_node_list', $this->params, 'processInfo');
		$multiClient->call('workflowatom', 'user/roleInfoList', array('status' => 1), 'roleInfo');
		$multiClient->call('atom', 'account/user_job_role_list', array('status' => 1), 'speedRoleInfo');
        $multiClient->callData();
		
        $typeInfo = $this->parseApiData($multiClient->typeInfo);
        $temp = $this->parseApiData($multiClient->processInfo);
		$roleInfo = $this->parseApiData($multiClient->roleInfo);
		$speedRoleInfo = $this->parseApiData($multiClient->speedRoleInfo);
		
		if(empty($typeInfo) || empty($typeInfo['type_parent_id'])) {
			$return = Response::gen_error(10002, '类型数据有误！', '类型数据有误！');
			return $this->app->response->setBody($return);
		}
		
		$processInfo = array();
		$processIdArr = array();
		if(!empty($temp)) {
			$processIdArr['process_id'] = array_keys($temp);
			foreach($temp as &$v) {
				$v['pre_process_ids_arr'] = explode(',', $v['pre_process_ids']);
				$v['next_process_ids_arr'] = explode(',', $v['next_process_ids']);
			}
			unset($v);
			
			foreach($temp as $v) {
				if(1 == $v['status']) {
					foreach($v['pre_process_ids_arr'] as $p) {
						$processInfo[$p][] = $v;
					}
				}
			}
		}
		
		//读取节点规则和动作信息
		$actionInfoOrigin = $ruleInfo = $this->actionInfo = array();
		
		if(!empty($processIdArr['process_id'])) {
			$multiClient = $this->getMultiClient();
			$multiClient->call('workflowatom', 'process/process_rule_list', $processIdArr, 'ruleInfo');
			$multiClient->call('workflowatom', 'process/process_action_list', $processIdArr, 'actionInfo');
			$multiClient->callData();
			
			$ruleInfoOrigin = $this->parseApiData($multiClient->ruleInfo);
			$actionInfoOrigin = $this->parseApiData($multiClient->actionInfo);
		}
		
		if(!empty($ruleInfoOrigin)) {
			foreach($ruleInfoOrigin as $v) {
				//$v['rule'] = html_entity_decode($v['rule']);
				//$v['rule'] = html_entity_decode($v['rule']);
				$ruleInfo[$v['process_id']][$v['next_process_id']] = $v;
			}
		}
		if(!empty($actionInfoOrigin)) {
			foreach($actionInfoOrigin as $v) {
				$this->actionInfo[$v['process_id']][$v['action_type']][$v['id']] = $v;
			}
		}
		//var_dump($ruleInfo);exit;
		$flowHtml = $this->genflowTab($processInfo);
		$actions = $this->getActionName('./../package/actionscript');
		
		$this->app->response->setBody(array(
			'typeInfo'		=> $typeInfo,
			'processInfo'	=> $processInfo,
			'processTab'	=> $temp,
			'flowHtml'		=> $flowHtml,
			'roleInfo'		=> $roleInfo,
			'processJson'	=> json_encode($temp),
			'ruleInfo'		=> json_encode($ruleInfo),
			'actionInfo'	=> json_encode($actionInfoOrigin),
			'actions'       => $actions,
			'speedRoleInfo'	=> $speedRoleInfo
		));
	}
	
	private function genflowTab($data, $rank = 0) {
		$html = '<table';
		if(0 == $rank) {
			$html .= '  id="process_nodes" ';
		}
		$html .='>';
		
		if(!empty($data[$rank])) {
			$html .= '<tr>';
			foreach($data[$rank] as $v) {
				$html .= '<td><div class="process_node" pid="' . $v['process_id'];
				$html .= '"><h5 class="process_title">（' . $v['process_id']. '）' ;
				$html .= $v['process_name'] . '</h5><p class="process_node_btn"><a href="#" class="edit_process" data-toggle="modal" data-target="#nodeEditModal">编辑信息</a></p>';
				$html .= '<table><tr><td><h6>下级节点</h6></td><td>';
				$html .= empty($v['next_process_ids']) ? '无' : $v['next_process_ids'];
				$html .= '</td></tr></table><p class="process_node_btn"><a href="#" class="edit_process_rule" data-toggle="modal" data-target="#ruleEditModal">编辑规则</a></p>';
				$html .= '<table><tr><td colspan="2"><h6>附加动作</h6></td></tr>';
				
				if(empty($this->actionInfo[$v['process_id']][1])) {
					$html .= '<tr><td>同意</td><td>暂无</td></tr>';
				}elseif(count($this->actionInfo[$v['process_id']][1]) == 1) {
					$first = array_pop($this->actionInfo[$v['process_id']][1]);
					$html .= '<tr><td>同意</td><td>' . $first['action_name'] . '</td></tr>';
				}else {
					$rowspan = count($this->actionInfo[$v['process_id']][1]);
					$first = array_shift($this->actionInfo[$v['process_id']][1]);
					$html .= '<tr><td rowspan="' . $rowspan . '">同意</td><td>' . $first['action_name'] . '</td></tr>';
					
					foreach($this->actionInfo[$v['process_id']][1] as $v) {
						$html .= '<tr><td>' . $v['action_name'] . '</td></tr>';
					}
				}
				
				if(empty($this->actionInfo[$v['process_id']][2])) {
					$html .= '<tr><td>驳回</td><td>暂无</td></tr>';
				}elseif(count($this->actionInfo[$v['process_id']][2]) == 1) {
					$first = array_pop($this->actionInfo[$v['process_id']][2]);
					$html .= '<tr><td>驳回</td><td>' . $first['action_name'] . '</td></tr>';
				}else {
					$rowspan = count($this->actionInfo[$v['process_id']][2]);
					$first = array_shift($this->actionInfo[$v['process_id']][2]);
					$html .= '<tr><td rowspan="' . $rowspan . '">驳回</td><td>' . $first['action_name'] . '</td></tr>';
					
					foreach($this->actionInfo[$v['process_id']][2] as $v) {
						$html .= '<tr><td>' . $v['action_name'] . '</td></tr>';
					}
				}
				
				$html .= '</table><p class="process_node_btn"><a href="#" class="edit_process_action" data-toggle="modal" data-target="#actionEditModal">编辑动作</a></p>';
				$html .= '</div></td>';
			}
			$html .= '</tr>';
			$html .= '<tr>';
			
			foreach($data[$rank] as $v) {
				$html .= '<td>';
				if(!empty($data[$v['process_id']])) {
					$html .= $this->genflowTab($data, $v['process_id']);
				}
				$html .= '</td>';
			}
			
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		return $html;
	}
	
	private function genflowTab11($data, $rank = 0) {
		$html = '<table';
		if(0 == $rank) {
			$html .= '  id="process_nodes" ';
		}
		$html .='>';
		
		if(!empty($data[$rank])) {
			$html .= '<tr>';
			foreach($data[0] as $v) {
				$html .= '<td><div class="process_node"><h5 class="process_title">';
				$html .= $v['process_name'] . '</h5><p><a href="#">编辑</a></p></div>';
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
		
		
		$html .= '</table>';
	}

	function getActionName($path) {

		$directory = new \DirectoryIterator($path);
		$file_name = array();

		foreach ($directory as $file) {

			$name = $file->getFilename();
			if ($name != '.' && $name != '..') {
				$name = explode('.', $name);
				$file_name[] = $name[0];
			}
		}

		return $file_name;
	}
	
	protected function _init() {
		
		$this->rules = array(
			'type_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            )
        );
		
		$this->params = $this->query()->safe();
	}

}