<?php
namespace Atom\Modules\Core;

use \Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Config;

class ConfigSearch extends \Atom\Modules\Common\BaseModule {

	//ID
	protected $params;

    public function run() {
		$this->_init();

		if (empty($this->params)) {
			$return = Response::gen_error(10001);
			return $this->app->response->setBody($return);
		}
		$result = Config::model()->getByPathOrFather($this->params);
		if ($result === FALSE) {
			$return = Response::gen_error(50001);
		}elseif (empty($result)) {
			$return = Response::gen_error(30001);
		}else{
			$return = Response::gen_success($result);
		}

		$this->app->response->setBody($return);
    }

	public function _init(){
        $data = $this->request->GET;
        $data_check = array(
			'path'=>array(
				'required'=>true,
				'type'=>'string',
			),
            'father_id'=>array(
                'required'=>true,
                'type'=>'integer',
            ),
            'all'=>array(
                'type'=>'integer',
            ),
            'count'=>array(
                'type'=>'integer',
            ),
		);
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->query()->safe();
	}

}

