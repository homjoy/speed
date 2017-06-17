<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Libs\Util\ArrayUtilities;
use Atom\Package\Common\Response;
use Atom\Package\Company\Company;
use Atom\Package\Company\City;

/**
 * 获取公司列表
 * @author hepang@meilishuo.com
 * @since 2015-04-09
 */

class CompanyList extends \Atom\Modules\Common\BaseModule {

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

        $queryParams = array();
        if (!empty($this->params['city_id'])) {
            if (strpos($this->params['city_id'], ',')) {
                $queryParams['city_id'] = explode(',', $this->params['city_id']);
            }else{
                $queryParams['city_id'] = intval($this->params['city_id']);
            }
        }
        if (!empty($this->params['company_id'])) {
            if (strpos($this->params['company_id'], ',')) {
                $queryParams['company_id'] = explode(',', $this->params['company_id']);
            }else{
                $queryParams['company_id'] = intval($this->params['company_id']);
            }
        }
        if ($this->params['status'] !== '') {
            $queryParams['status'] = $this->params['status'];
        }
        if (!empty($this->params['count'])) {
            $queryParams['count'] = $this->params['count'];
        }
        $result = Company::getInstance()->getCompanyList($queryParams, $this->params['page'], $this->params['page_size']);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
            if ($this->params['count']!=1) {
                //增加城市名称
                $cityIds = ArrayUtilities::my_array_column($result, 'city_id');
                $cityInfos = City::getInstance()->getCityList(array('city_id'=>$cityIds), 0, $this->params['page_size']);
                if (!empty($cityInfos)) {
                    foreach ($result as $key => $value) {
                        $cityId = $value['city_id'];
                        isset($cityInfos[$value['city_id']]) && $result[$key]['city_name'] = $cityInfos[$value['city_id']]['city_name'];
                    }
                }

                $return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
            }else{
                $return = Response::gen_success($result);
            }

        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'city_id'=>array(
                'type'=>'string',
            ),
            'company_id'=>array(
                'type'=>'string',
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'page'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'page_size'=>array(
                'type'=>'integer',
                'default'=>20,
            ),
            'count'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }








}