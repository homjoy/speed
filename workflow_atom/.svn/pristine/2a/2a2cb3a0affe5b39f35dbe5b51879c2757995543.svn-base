<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\ProcessRule;

class ProcessRuleList extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		$this->_init();
		
		$this->sample = ProcessRule::getInstance()->getFields();
		
		if(empty($this->params['process_id'])) {
			$return = Response::gen_error(10002, '', 'process_id 数据有误！');
			return $this->app->response->setBody($return);
		}
		
		$result = ProcessRule::getInstance()->getDataList(array(
			'process_id'	=> $this->params['process_id']
		));
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result) && !is_array($result)) {
        	$return = Response::gen_error(30001);
        }else {
			$return = Response::gen_success(Format::outputData($result, $this->sample, true));
		}
		
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		$this->params['process_id'] = isset($this->request->POST['process_id']) ? $this->request->POST['process_id'] : array();
	}

}