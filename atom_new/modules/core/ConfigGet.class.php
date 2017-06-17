<?php
namespace Atom\Modules\Core;

use \Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Config;

class ConfigGet extends \Atom\Modules\Common\BaseModule {

	//ID
	protected $id;

    public function run() {
		$this->_init();

		if (empty($this->id)) {
			$return = Response::gen_error(10001);
			return $this->app->response->setBody($return);
		}
		$result = Config::model()->getDataById($this->id);
		if ($result === FALSE) {
			$return = Response::gen_error(50001);
		}elseif (empty($result)) {
			$return = Response::gen_error(30001);
		}else{
			$return = Response::gen_success(Format::outputData($result, Config::sample, TRUE));
		}

		$this->app->response->setBody($return);
    }

	public function _init(){
		$this->rules = array(
			'id'=>array(
				'required'=>true,
				'type'=>'integer',
			),
		);
		$this->id = $this->query()->safe();
	}

}

