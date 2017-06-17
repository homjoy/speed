<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\ProcessRule;

class ProcessRuleDelete extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		if(!$this->_init()) {
			return;
		}
		
		foreach($this->params['rule_id'] as $k => $v) {
			if( empty($v) ) {
				unset($this->params['rule_id'][$k]);
			}
		}
		
		//参数校验
        if(empty($this->params['rule_id'])) {
			$return = Response::gen_error(10001, '参数异常');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		$this->sample = ProcessRule::getInstance()->getFields();
		
		$result = ProcessRule::getInstance()->deleteDataById($this->params['rule_id']);
		
		//return $this->app->response->setBody($result);
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
		$this->params['rule_id'] = isset($this->request->POST['rule_id']) ? $this->request->POST['rule_id'] : '';
		
		$this->params['rule_id'] = explode(',', $this->params['rule_id']);
		
		if(empty($this->params['rule_id'])) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}

}