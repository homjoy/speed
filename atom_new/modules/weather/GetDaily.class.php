<?php
namespace Atom\Modules\Weather;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Weather\WeatherDaily;

/**
 * 获取每日天气
 * @author hepang@meilishuo.com
 * @since 2015-06-24
 */

class GetDaily extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = array(
            'dt'        => 0,
            'date'      => '',
            'date_cn'   => '',
            'temp'      => array(),     //温度
            'humidity'  => 0,           //湿度
            'pressure'  => 0.00,        //压力
            'weather'   => array(),
            'speed'     => 0.0,
            'deg'       => 0.0,
            'clouds'    => 0,
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
        if (!empty($this->params['date'])) {
            $queryParams['date'] = $this->params['date'];
        }else{
            $queryParams['date'] = date('Y-m-d');
        }

        //查询
        $result = WeatherDaily::getInstance()->getDataList($queryParams, $this->params['limit']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{

            $result = self::_assemble($result);
            $return = $result;
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
            'city_id'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'date'=>array(
                'type'=>'string',
            ),
            'limit'=>array(
                'type'=>'integer',
                'default'=>3,
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

        foreach ($data as $key => $value) {
            $weather = json_decode($value['data'], TRUE);
            $weather['weather']   = isset($weather['weather'][0]) ? $weather['weather'][0] : $weather['weather'];
            $weather['date'] = $value['date'];
            $day = date('N', $weather['dt']);
            $weather['date_cn'] = self::_formatWeek($day);

            $weather['temp']['min'] = round($weather['temp']['min']);
            $weather['temp']['max'] = round($weather['temp']['max']);
            $data[$key] = $weather;
        }
        return $data;
    }

    /**
     * 数据格式化
     */
    protected function _formatWeek($num=0)
    {
        if (empty($num)) {
            return FALSE;
        }

        switch ($num) {
            case 1:
                $day = '星期一';
                break;
            case 2:
                $day = '星期二';
                break;
            case 3:
                $day = '星期三';
                break;
            case 4:
                $day = '星期四';
                break;
            case 5:
                $day = '星期五';
                break;
            case 6:
                $day = '星期六';
                break;
            case 7:
                $day = '星期天';
                break;
            
            default:
                $day = '星期X';
                break;
        }

        return $day;
    }
}
