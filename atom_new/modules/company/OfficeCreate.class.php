<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\Office;

/**
 * 新建办公室
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

class OfficeCreate extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = Office::getInstance()->getFields();

        //参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

        if (empty($this->params['city_id']) || empty($this->params['company_id']) || empty($this->params['office_name'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        $result = Office::getInstance()->insert($this->params);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
            $this->params['office_id'] = $result;
        	$return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'city_id' => array(
                'required'=>true,
                'type'=>'integer',
            ),
            'company_id' => array(
                'required'=>true,
                'type'=>'integer',
            ),
            'office_name'=> array(
                'required'=>true,
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'office_floor'=> array(
                'type'=>'integer',
                'maxLength'=> 5,
            ),
            'office_position'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'office_address'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'office_telephone'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'office_fax'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'office_capacity'=> array(
                'type'=>'integer',
                'maxLength'=> 5,
            ),
            'office_detail'=> array(
                'type'=>'string',
                'maxLength'=> 300,
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
        );

        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }








}