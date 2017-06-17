<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Libs\Util\ArrayUtilities;
use Atom\Package\Common\Response;
use Atom\Package\Company\Company;
use Atom\Package\Company\City;

/**
 * 获取分公司
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

class CompanyGet extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = Company::getInstance()->getFields();
        $this->sample['city_name'] = '未知';

        if($this->query()->hasError()){
        	$return = Response::gen_error(10001, '', $this->query()->getErrors());
        	return $this->app->response->setBody($return);
        }

        if (empty($this->params['company_id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

        $result = Company::getInstance()->getCompanyById($this->params['company_id']);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{

            //增加城市名称
            $cityInfo = City::getInstance()->getCityList(array('city_id'=>$result['city_id']), 0, 1);
            if (!empty($cityInfo)) {
                $cityInfo = current($cityInfo);
                $result['city_name'] = $cityInfo['city_name'];
            }

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
            'company_id'=>array(
                'required'=>true,
                'type'=>'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }









}