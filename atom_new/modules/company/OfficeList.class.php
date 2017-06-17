<?php
namespace Atom\Modules\Company;

use Atom\Package\Company\City;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\Office;

/**
 * 获取办公室列表
 * @author hepang@meilishuo.com
 * @since 2015-04-09
 */

class OfficeList extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = Office::getInstance()->getFields();
        $this->sample['city_name'] = '未知';

        $queryParams = array();
        if (!empty($this->params['city_id'])) {
            $queryParams['city_id'] = $this->params['city_id'];
        }
        if (!empty($this->params['company_id'])) {
            $queryParams['company_id'] = $this->params['company_id'];
        }
        if (!empty($this->params['office_id'])) {
            $queryParams['office_id'] = $this->params['office_id'];
        }
        if ($this->params['status'] !== '') {
            $queryParams['status'] = $this->params['status'];
        }

        $result = Office::getInstance()->getOfficeList($queryParams, $this->params['page'], $this->params['page_size']);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
            //增加城市名称
            $cityIds = ArrayUtilities::my_array_column($result, 'city_id');
            $cityInfos = City::getInstance()->getCityList(array('city_id'=>$cityIds), 0, $this->params['page_size']);
            if (!empty($cityInfos)) {
                foreach ($result as $key => $value) {
                    $cityId = $value['city_id'];
                    isset($cityInfos[$cityId]) && $result[$key]['city_name'] = $cityInfos[$cityId]['city_name'];
                }
            }

        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
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
                'type'=>'integer',
            ),
            'city_id'=>array(
                'type'=>'integer',
            ),
            'company_id'=>array(
                'type'=>'integer',
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
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }








}