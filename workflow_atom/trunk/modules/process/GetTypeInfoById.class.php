<?php
namespace WorkFlowAtom\Modules\Process;

use Libs\Util\Format;
use \WorkFlowAtom\Package\Process\TaskType;
use \WorkFlowAtom\Package\Common\Response;

class GetTypeInfoById extends \WorkFlowAtom\Modules\Common\BaseModule{
	
	private $sample;
	private $params;

    public function run() {
		
		$this->_init();
		
		//参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->query()->getErrors());
        	return $this->app->response->setBody($return);
        }
		
		if(empty($this->params['type_id'])) {
			$return = Response::gen_error(10002, '', 'type_id 数据有误！');
			return $this->app->response->setBody($return);
		}
        
		$this->sample = TaskType::getInstance()->getFields();
		
        $result = TaskType::getInstance()->getDataById($this->params['type_id']);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result) && !is_array($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample));
        }

    	$this->app->response->setBody($return);
        
    }
	
	protected function _init() {
		
		$this->rules = array(
			'type_id'	=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'default'	=> 0,
            )
        );
		
		$this->params = $this->post()->safe();
	}
}