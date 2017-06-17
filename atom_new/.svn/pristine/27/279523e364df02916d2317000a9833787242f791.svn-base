<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Config;

/**
 * 获取配置信息列表
 * @author minggeng@meilishuo.com
 * @since 2015-04-14
 */

class ConfigGetValue extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();
		$this->sample   = Config::model()->getFields();

		$queryParams = $this->query()->params_filter($this->params);

        $result = Config::model()->getValue($queryParams);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success($result['value']);
        }

    	$this->app->response->setBody($return);
	}	

	/**
	 * 参数初始化
	 */
	protected function _init(){
		$this->rules = array(
			'path'=>array(
				'type'=>'string',
				'required'  => true,
			),
		);
		$this->params   = $this->query()->safe();
	}

}
