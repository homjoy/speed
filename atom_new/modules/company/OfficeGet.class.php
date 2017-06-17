<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\Office;

/**
 * 获取办公室
 * @author hepang@meilishuo.com
 * @since 2015-04-09
 */

class OfficeGet extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = Office::getInstance()->getFields();

        if($this->query()->hasError()){
        	$return = Response::gen_error(10001, '', $this->query()->getErrors());
        	return $this->app->response->setBody($return);
        }

        if (empty($this->params['office_id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        $result = Office::getInstance()->getOfficeById($this->params['office_id']);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample));
        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'office_id'=>array(
                'required'=>true,
                'type'=>'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }









}