<?php

namespace Worker\Scripts\Weather;

use Worker\Package\Weather\City;
use Worker\Package\Weather\WeatherHourly;
use Cmfcmf\OpenWeatherMap;

class GrabWeatherHourly extends \Frame\Script {

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
     * 抓取小时天气
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function grab($owm, $query, $units, $lang){

        $forecast = $owm->getRawHourlyForecastData($query, $units, $lang, null, 'json');
        $data = json_decode($forecast, true);
        $list = $data['list'];
/*        
        $t = array();
        foreach ($list as $key => $value) {
            $d = date('Y-m-d H:i:s', $value['dt']);
            $t[$d] = $value;
        }
        return $t;
*/
        return $list;
    }

    /**
     * 检测是否已抓取
     * @param $query
     * @return bool
     * @throws \Exception
     */
    public function check($city_id, $timestamp){

        $res = WeatherHourly::model()->getData($city_id, $timestamp);

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

            //判断时间
            //只保存24小时以内的数据
            $now = time();
            $dt_time = strtotime($value['dt_txt']);
            if ($dt_time - $now > 3600*24) {
                continue;
            }

            //检测是否已抓取
            $check = self::check($city_id, $value['dt_txt']);
            if (!empty($check)) {
                $updateTime = strtotime($check['update_time']);
                //更新时间大于12小时就更新一次
                if ($now - $updateTime > 3600*12) {
                    $data = json_encode($value);
                    $updateData = array();
                    $updateData['id']          = $check['id'];
                    $updateData['data']        = $data;
                    $res = WeatherHourly::model()->update($updateData);

                    if ($res) {
                        echo 'city_id: ', $city_id, ' timestamp:', $value['dt_txt'], " is updated \n";
                    }else{
                        echo 'city_id: ', $city_id, ' timestamp:', $value['dt_txt'], " is not changed \n";
                    }

                }else{
                    continue;
                }

                //echo 'city_id: ', $city_id, ' dt_txt:', $value['dt_txt'], " is found \n";
                continue;
            }else{

                $d = json_encode($value);
                $newData = array();
                $newData['city_id']     = $city_id;
                $newData['timestamp']   = $value['dt_txt'];
                $newData['data']        = $d;
                $res = WeatherHourly::model()->create($newData);

                if ($res) {
                    echo 'city_id: ', $city_id, ' timestamp:', $value['dt_txt'], " is inserted \n";
                }else{
                    echo 'city_id: ', $city_id, ' timestamp:', $value['dt_txt'], " is failed \n";
                }

            }
        }

        return TRUE;
    }



}
