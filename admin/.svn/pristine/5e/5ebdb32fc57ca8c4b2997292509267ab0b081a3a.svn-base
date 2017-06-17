<?php
namespace Admin\Modules\Workflow\Process;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Workflow\TaskType;
use Libs\Util\Format;

class AjaxTaskTypeEdit extends BaseModule {
	
	private $params = array();
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		$this->_init();
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		$data = $this->getClient()->call('workflowatom', 'process/task_type_edit', $this->params);
		$return = $this->parseApiData($data);
		
		if($return !== FALSE) {
			$this->app->response->setBody(Response::gen_success($return));
		}
	}
	
	protected function _init() {
		
		$this->rules = array(
			'type_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
            'type_name'	=> array(
                'required'		=> true,
                'type'			=> 'string',
                'maxLength'		=> 30,
				'allowEmpty'	=> false
            ),
            'status'	=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
			'type_parent_id'	=> array(
                'type'		=> 'integer',
                'default'	=> 0,
            ),
        );
		
		$this->params = $this->post()->safe();
	}

}