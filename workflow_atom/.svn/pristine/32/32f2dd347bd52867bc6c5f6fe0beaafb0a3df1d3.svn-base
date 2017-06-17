<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\ProcessAction;

class ProcessActionDelete extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		if(!$this->_init()) {
			return;
		}
		
		foreach($this->params['action_id'] as $k => $v) {
			if( empty($v) ) {
				unset($this->params['action_id'][$k]);
			}
		}
		
		//参数校验
        if(empty($this->params['action_id'])) {
			$return = Response::gen_error(10001, '参数异常');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		$this->sample = ProcessAction::getInstance()->getFields();
		
		$result = ProcessAction::getInstance()->update(array(
			'id'		=> $this->params['action_id'],
			'status'	=> 0
		));
		
		if ($result === FALSE) {
			$return = Response::gen_error(50001);
			$this->app->response->setBody($return);
			return;
		}elseif (empty($result)) {
			$return = Response::gen_error(30001);
			$this->app->response->setBody($return);
			return;
		}
		
		$return = Response::gen_success($return);
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		$this->params['action_id'] = isset($this->request->POST['action_id']) ? $this->request->POST['action_id'] : '';
		
		$this->params['action_id'] = explode(',', $this->params['action_id']);
		
		if(empty($this->params['action_id'])) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}

}