<?php 
namespace WorkFlowApi\Modules\Task;

/**
 * 任务创建
 * @author yixiangwang@meilishuo.com
 * @since 2015-07-30
 */

use WorkFlowApi\Modules\Common\BaseModule;
use WorkFlowApi\Package\Common\Response;

class TaskTypeList extends BaseModule {

	private $params;
	
	public function run() {
	
		$this->_init();
	
		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		$data = $this->getClient()->call('workflowatom', 'process/task_type_list', array());
		$data = $this->parseApiData($data);
		
		if($data === FALSE) {
			return;
		}
		
		$tree = $this->genTree($data);
		
		$return = array();
		if(!empty($this->params['type_id'])) {
			if(!empty($tree[$this->params['type_id']])) {
				$return = $tree[$this->params['type_id']];
			}elseif(!empty($data[$this->params['type_id']])) {
				$return = $data[$this->params['type_id']];
			}
		}else {
			$return = $tree;
		}
		
		$this->app->response->setBody(Response::gen_success($return));
	}
	
	private function genTree($data) {
		$tree = array();
		$parent_arr = array();
		
		if(!empty($data)) {
			foreach($data as $v) {
				$parent_arr[$v['type_parent_id']][] = $v;
			}
			
			foreach($parent_arr[0] as $v) {
				$tree[$v['type_id']] = $v;
				
				$tree[$v['type_id']]['sub'] = array();
				if(!empty($parent_arr[$v['type_id']])) {
					$tree[$v['type_id']]['sub'] = $parent_arr[$v['type_id']];
				}
			}
		}
		
		return $tree;
	}
	
	private function _init() {
	
		$this->rules = array(
			'type_id'	=> array(
				'type'		=> 'integer',
				'default'	=> 0,
			)
		);
		
		$this->params = $this->post()->safe();
	}
}