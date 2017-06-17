<?php

namespace Worker\Scripts\Weather;

use Worker\Package\Weather\City;
use Worker\Package\Weather\WeatherToday;
use Cmfcmf\OpenWeatherMap;

class GrabWeatherToday extends \Frame\Script {

    public function run() {

        //获取城市
        $citys = City::model()->getAllData();
        if (empty($citys)) {
            return FALSE;
        }

        // Language of data (try your own language here!):
        $lang = 'zh_cn';
        // Units (can be 'metric' or 'imperial' [default]):
        $units = 'metric';
        // Get OpenWeatherMap object. Don't use caching (take a look into Example_Cache.php to see how it works).
        $owm = new OpenWeatherMap();

        //抓取
        foreach ($citys as $city) {
            if (!empty($city['weather_id'])) {
                $query = trim($city['weather_id']);
            }elseif (!empty($city['pinyin'])) {
                $query = trim($city['pinyin']);
            }else{
                continue;
            }

            //抓取结果
            $data = self::grab($owm, $query, $units, $lang);

            //判断数据
            if (empty($data)) {
                continue;
            }

            $result = self::save($city['city_id'], $data);

        }

        global $start;
        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    /**
     * 抓取每天天气
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function grab($owm, $query, $units, $lang){

        $forecast = $owm->getRawWeatherData($query, $units, $lang, null, 'json', 4);
        $data = json_decode($forecast, true);
        $list = $data['sys'];
        return $list;
    }

    /**
     * 检测是否已抓取
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function check($city_id, $timestamp){

        $res = WeatherToday::model()->getData($city_id, $timestamp);

        return $res;
    }

    /**
     * 保存天气
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function save($city_id, $data)
    {
        if (empty($city_id) && empty($data)) {
            return FALSE;
        }

        if (empty($data['id'])) {
            return FALSE;
        }

        //检测是否已抓取
        $now = time();
        $date = date('Y-m-d');
        $check = self::check($city_id, $date);
        if (!empty($check)) {
            echo 'city_id: ', $city_id, ' date:', $date, " is existed \n";
            return FALSE;
        }else{

            $data = json_encode($data);
            $newData = array();
            $newData['city_id']     = $city_id;
            $newData['date']        = $date;
            $newData['data']        = $data;
            $res = WeatherToday::model()->create($newData);

            if ($res) {
                echo 'city_id: ', $city_id, ' date:', $date, " is inserted \n";
            }else{
                echo 'city_id: ', $city_id, ' date:', $date, " is failed \n";
            }                
        }


        return TRUE;
    }



}
