<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\Company;

/**
 * 更新公司信息
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

class CompanyUpdate extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = Company::getInstance()->getFields();

        //参数校验
        if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->getErrors());
        	return $this->app->response->setBody($return);
        }

        if (empty($this->params['company_id']) || empty($this->params['city_id']) || empty($this->params['company_name'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        foreach ($this->params as $key => $value) {
            if (!empty($value)) {
                $queryParams[$key] = $value;
            }
        }
        if ($this->params['status'] !== '') {
            $queryParams['status'] = $this->params['status'];
        }

        $result = Company::getInstance()->update($queryParams);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
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
            'company_id' => array(
                'required'=>true,
                'type'=>'integer',
            ),
            'city_id' => array(
                'required'=>true,
                'type'=>'integer',
            ),
            'company_name'=> array(
                'required'=>true,
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'company_address'=> array(
                'type'=>'string',
                'maxLength'=> 50,
            ),
            'company_addr'=> array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'telephone'=> array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'fax'=> array(
                'type'=>'string',
                'maxLength'=> 30,
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