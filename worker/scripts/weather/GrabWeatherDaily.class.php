<?php

namespace Worker\Scripts\Weather;

use Worker\Package\Weather\City;
use Worker\Package\Weather\WeatherDaily;
use Cmfcmf\OpenWeatherMap;

class GrabWeatherDaily extends \Frame\Script {

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

        $forecast = $owm->getRawDailyForecastData($query, $units, $lang, null, 'json', 4);
        $data = json_decode($forecast, true);
        $list = $data['list'];
        return $list;
    }

    /**
     * 检测是否已抓取
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function check($city_id, $timestamp){

        $res = WeatherDaily::model()->getData($city_id, $timestamp);

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

        foreach ($data as $value) {

            if (empty($value['weather'])) {
                continue;
            }

            //检测是否已抓取
            $now = time();
            $date = date('Y-m-d', $value['dt']);
            $check = self::check($city_id, $date);
            if (!empty($check)) {
                $updateTime = strtotime($check['update_time']);
                //更新时间大于12小时就更新一次
                if ($now - $updateTime > 3600*24) {
                    $data = json_encode($value);
                    $updateData = array();
                    $updateData['id']          = $check['id'];
                    $updateData['data']        = $data;
                    $res = WeatherDaily::model()->update($updateData);

                    if ($res) {
                        echo 'city_id: ', $city_id, ' date:', $date, " is updated \n";
                    }else{
                        echo 'city_id: ', $city_id, ' date:', $date, " is not changed \n";
                    }

                }else{
                    continue;
                }
            }else{

                $data = json_encode($value);
                $newData = array();
                $newData['city_id']     = $city_id;
                $newData['date']        = $date;
                $newData['data']        = $data;
                $res = WeatherDaily::model()->create($newData);

                if ($res) {
                    echo 'city_id: ', $city_id, ' date:', $date, " is inserted \n";
                }else{
                    echo 'city_id: ', $city_id, ' date:', $date, " is failed \n";
                }                
            }
        }

        return TRUE;
    }



}
