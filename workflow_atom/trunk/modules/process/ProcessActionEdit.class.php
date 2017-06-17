<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\ProcessAction;

class ProcessActionEdit extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		if(!$this->_init()) {
			return;
		}
		
		foreach($this->params as $k => $v) {
			if( empty($v['id']) ) {
				unset($this->params[$k]);
			}
		}
		
		//参数校验
        if(empty($this->params)) {
			$return = Response::gen_error(10001, '参数异常');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		$this->sample = ProcessAction::getInstance()->getFields();
		
		$return = array();
		foreach($this->params as $k => $v) {
			$result = ProcessAction::getInstance()->update($v);
			if ($result === FALSE) {
				$return = Response::gen_error(50001);
				$this->app->response->setBody($return);
				return;
			}elseif (empty($result)) {
				$return = Response::gen_error(30001);
				$this->app->response->setBody($return);
				return;
			}
		}
		
		$return = Response::gen_success(Format::outputData($this->params, $this->sample, true));
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		$this->params = isset($this->request->POST['data']) ? $this->request->POST['data'] : array();
		
		if(empty($this->params)) {
			$return = Response::gen_error(10001, '参数错误');
        	$this->app->response->setBody($return);
			
			return FALSE;
		}
		
		return TRUE;
	}

}