<?php
namespace Admin\Modules\Workflow\Process;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\TaskType;

class Index extends BaseModule {

	protected $checkUserPermission = TRUE;

	public function run() {
		$data = $this->getClient()->call('workflowatom', 'process/task_type_list', array());
		$data = $this->parseApiData($data);
		
		$return = $this->genTree($data);
		
		$this->app->response->setBody(array(
			'data'	=> $return
		));
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

}