<?php
namespace Atom\Modules\Weather;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Weather\WeatherHourly;
use Atom\Package\Weather\WeatherToday;

/**
 * 获取每时天气
 * @author hepang@meilishuo.com
 * @since 2015-07-07
 */

class GetHourly extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = array(
            'city_id'   => 0,
            'dt'        => 0,
            'timestamp' => '',
            'sys'       => array(),
            'main'      => array(),
            'weather'   => array(),
            'clouds'    => array(),
            'wind'      => array(),
        );

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        if (!empty($this->params['city_id'])) {
            $queryParams['city_id'] = $this->params['city_id'];
        }
        if (!empty($this->params['timestamp'])) {
            $queryParams['timestamp'] = $this->params['timestamp'];
        }else{
            $queryParams['timestamp'] = date('Y-m-d H:i:s');
        }

        //查询
        $result = WeatherHourly::getInstance()->getDataList($queryParams, $this->params['limit']);

        $queryParams['date'] = date('Y-m-d');
        $today  = WeatherToday::getInstance()->getDataList($queryParams, 1);

        if ($result === FALSE || $today === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{

            $result = self::_assemble($result);
            $result['sys'] = self::_assembleToday($today);
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
            'city_id'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'timestamp'=>array(
                'type'=>'string',
            ),
            'limit'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

    /**
     * 数据格式化
     */
    protected function _assemble($data)
    {
        if (empty($data)) {
            return FALSE;
        }

        $data   = current($data);
        $weather = json_decode($data['data'], TRUE);
        $weather['timestamp'] = $data['timestamp'];
        $weather['city_id']   = $data['city_id'];
        $weather['weather']   = isset($weather['weather'][0]) ? $weather['weather'][0] : $weather['weather'];
        //var_dump($weather);
        return $weather;
    }

    /**
     * 数据格式化
     */
    protected function _assembleToday($data)
    {
        if (empty($data)) {
            return FALSE;
        }

        $data   = current($data);
        $weather = json_decode($data['data'], TRUE);
        //var_dump($weather);
        return $weather;
    }

}
