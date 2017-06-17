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

class ConfigGetChild extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();
		$this->sample   = Config::model()->getFields();

		$queryParams = $this->query()->params_filter($this->params);

        $result = Config::model()->getChild($queryParams);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
			$res = $this->_filter($result);
        	$return = Response::gen_success($res);
        }

    	$this->app->response->setBody($return);
	}	

	/**
	 * 返回值处理
	 */
	private function _filter($data){
		$res = array();
		foreach($data as $d){
			$res[$d['key']] =  $d['value'];
		}
		return $res;
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
