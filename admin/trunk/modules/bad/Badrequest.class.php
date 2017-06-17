<?php
namespace Admin\Modules\Bad;

use Admin\Package\Common\Response;
use Admin\Modules\Common\BaseModule;

class Badrequest extends BaseModule {

	public function run() {
		$this->_init();
        
        $return = Response::gen_error($this->params['error_code']);
        
        $this->app->response->setBody(array( 'data' => $return));

	}

	private function _init(){
        $this->rules = array(
            'error_code'   => array(
                'required'  => false,
                'type'      => 'integer',
                'default'   => 0,
            ),
            
        );
        
        $this->params = $this->query()->safe();
    }

}
