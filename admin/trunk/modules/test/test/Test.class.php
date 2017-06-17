<?php
namespace Admin\Modules\Test\Test;

use Libs\Util\Format;

class Test extends \Admin\Modules\Common\BaseModule {

	public function run() {
        $return = array('hello' => 'hello ', 'name' => 'speed');
        $this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init(){}

}
