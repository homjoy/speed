<?php
namespace Apicloud\Modules\Weather;

use Libs\Util\Format;
use Apicloud\Package\Common\Response;
use Apicloud\Package\Weather\CityWeather;
use Apicloud\Package\Company\City;

/**
 * 获取会议室列表
 * @author hepang@meilishuo.com
 * @since 2015-07-08
 */

class Get extends \Apicloud\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

	public function run() {

        $this->_init();

        //默认参数
        $queryParams = array();

        if (!empty($this->params['city_id'])) {
            $queryParams['city_id'] = $this->params['city_id'];
        }

        //查询
        $result = CityWeather::getInstance()->getAllWeather($queryParams);
        $city = City::getInstance()->getCityInfo($queryParams);

        if ($result === FALSE || $city === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
            $result['city'] = current($city);
        	//$return = Response::gen_success(Format::outputData($return, $this->sample, TRUE));
            $return = $result;
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
                'default'=>1,
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }








}