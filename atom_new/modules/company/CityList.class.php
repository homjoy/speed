<?php
namespace Atom\Modules\Company;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Company\City;

/**
 * 获取城市列表
 * @author hepang@meilishuo.com
 * @since 2015-04-09
 */

class CityList extends \Atom\Modules\Common\BaseModule {

    protected $params;
	protected $errors   = array();
    private $sample;

	public function run() {

        $this->_init();

        $this->sample = City::getInstance()->getFields();

        $queryParams = array();
        if ($this->params['status'] !== '') {
            $queryParams['status'] = $this->params['status'];
        }
		if (!empty($this->params['city_id'])) {
			$queryParams['city_id'] = $this->params['city_id'];
		}
		if (!empty($this->params['city_name'])) {
			$queryParams['city_name'] = $this->params['city_name'];
		}
        if (!empty($this->params['count'])) {
            $queryParams['count'] = $this->params['count'];
        }
        $result = City::getInstance()->getCityList($queryParams, $this->params['page'], $this->params['page_size']);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
            if ($this->params['count']!=1) {
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
                'type'=>'multiId',
            ),
            'city_name'=>array(
                'type'=>'multiId',
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
            'all'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }









}
