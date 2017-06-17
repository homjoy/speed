<?php

namespace Worker\Scripts\Weather;

use Worker\Package\Weather\City;
use Worker\Package\Weather\WeatherDaily;
use Libs\Util\ArrayUtilities;
/*
 * 获取未来3天的天气情况
 *  author haibinzhou@meilishuo.com
 *  date 2016-02-17
 *  每天跑一次脚本
 */
class GetWeatherDaily extends \Frame\Script {
    const CLEAR = 800;   //晴天
    const SLEET = 350;   //雨夹雪
    const RAIN  = 550;   //雨
    const SNOW  = 650;   //雪
    const FOG   = 750;   //雾
    const WIND  = 900;   //风

    public function run() {
        //获取城市
        $citys = City::model()->getAllData();
        if (empty($citys)) {
            return FALSE;
        }
        $future_three_days = array();
        for($i=1;$i<4;$i++){  //获取未来三天的天气，此数组是未来三天的日期
            $future_three_days[$i] = date('Y-m-d',strtotime('+'.$i.' day'));
        }

        $weatherInfo = array();
        foreach($citys as $key=>$val){
            $city = explode(',',$val['pinyin']);
            $weather = $this->getWeatherInfos($city[0]);
            if(empty($weather)){
                continue;
            }
            $weatherInfo['city_id'] = $val['city_id'];

            $weather['daily_forecast'] = ArrayUtilities::hashByKey(isset($weather['daily_forecast'])?$weather['daily_forecast']:array(),'date');

            foreach($future_three_days as $key=>$value){
                if(isset($weather['daily_forecast'][$value])){
                    $weatherInfo['date'] = $weather['daily_forecast'][$value]['date'];
                    $weather_temp['dt'] = strtotime($weatherInfo['date']);
                    $weather_temp['wind'] = $weather['daily_forecast'][$value]['wind'];
                    $weather_temp['temp']['max'] = $weather['daily_forecast'][$value]['tmp']['max'];
                    $weather_temp['temp']['min'] = $weather['daily_forecast'][$value]['tmp']['min'];
                    $weather_temp['cond'] = $weather['daily_forecast'][$value]['cond'];
                    $weather_temp['weather']['id'] = $this->weatherId($weather['daily_forecast'][$value]);
                    $weatherInfo['data'] = json_encode($weather_temp);
                    $check = $this->check($val['city_id'],$weatherInfo['date']);
                    if($check){  //用来检测是否抽入过
                        WeatherDaily::model()->create($weatherInfo);
                    }
                }
            }
        }

        global $start;
        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //调用天气接口
    public function getWeatherInfos($city){
        $city = strtolower($city);
        $ch = curl_init();
        $url = 'http://apis.baidu.com/heweather/weather/free?city='.$city;
        $header = array(
            'apikey: fab2ff5cf6b03bbc683e735488443e2f',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        $res = json_decode($res,true);
        if(!empty($res)){
            $res = array_pop($res);
            $res = !empty($res) ? array_shift($res) : array();
        }

        return $res;
    }

    //整理成需要的数据格式
    public function weatherId($weather){
        $weather_id = self::CLEAR;
        if(empty($weather)){
            return $weather_id;
        }

        if(isset($weather['cond']) && !empty($weather['cond'])){
            if(strpos($weather['cond']['txt_d'],'晴') !== false){
                $weather_id = self::CLEAR;
            }elseif(strpos($weather['cond']['txt_d'],'雨') !== false){
                $weather_id = self::RAIN;
            }elseif(strpos($weather['cond']['txt_d'],'雪') !== false){
                $weather_id = self::SNOW;
            }elseif(strpos($weather['cond']['txt_d'],'雾') !== false){
                $weather_id = self::FOG;
            }elseif(strpos($weather['cond']['txt_d'],'风') !== false){
                $weather_id = self::WIND;
            }elseif(strpos($weather['cond']['txt_d'],'雨') !== false && strpos($weather['cond']['txt'],'雪') !==false){
                $weather_id = self::SLEET;
            }else{
                $weather_id = self::CLEAR;
            }
        }

        return $weather_id;
    }

    public function check($city_id,$date){
        $check = true;
        $weatherFirst = WeatherDaily::model()->getData($city_id,$date);
        if(!empty($weatherFirst)){
            $check = false;
        }

        return $check;
    }


}
