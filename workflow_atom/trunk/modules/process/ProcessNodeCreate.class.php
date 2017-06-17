<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Common\Response;
use \WorkFlowAtom\Package\Process\Process;

class ProcessNodeCreate extends \WorkFlowAtom\Modules\Common\BaseModule {
	
	private $params;
	private $sample;

	public function run() {
		
		$this->_init();
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		$this->sample = Process::getInstance()->getFields();
		
		$this->params['pre_process_ids'] = 0;
		$this->params['next_process_ids'] = 0;
		
		$result = Process::getInstance()->insert($this->params);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else {
			$this->params['process_id'] = $result;
            
        	$return = Response::gen_success(Format::outputData($this->params, $this->sample));
		}
		
		$this->app->response->setBody($return);
	}
	
	protected function _init() {
		
		$this->rules = array(
			'tasktype_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            ),
            'process_name'	=> array(
                'required'		=> true,
                'type'			=> 'string',
                'maxLength'		=> 30,
				'allowEmpty'	=> false
            ),
            'status'	=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
			'role_id'	=> array(
                'type'		=> 'string',
                'default'	=> 0,
            ),
        );
		
		$this->params = $this->post()->safe();
	}

}