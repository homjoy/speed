<?php
namespace Apicloud\Package\Weather;

/**
 * Weather
 * @package Apicloud\Package\Weather
 * @author hepang@meilishuo.com
 * @since 2015-07-07
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class CityWeather extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 600;

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 查询城市
     */
    public static function getAllWeather($params = array()) {
        //查询缓存
        $cacheKey = 'Weather:getAllWeather:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $dailyWeather = self::getDailyWeather($params);
        $hourlyWeather = self::getHourlyWeather($params);

        if (!$dailyWeather || !$hourlyWeather) {
            return FALSE;
        }

        $result = array(
            'today'     => $hourlyWeather,
            'forecast'  => $dailyWeather,
        );

        //生成缓存
        BaseMemcache::instance()->set($cacheKey, $result, self::$expireTime);

        return $result;
    }

    /**
     * 查询城市
     */
    public static function getDailyWeather($params = array()) {
        //查询缓存
        $cacheKey = 'Weather:getDailyWeather:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'weather/get_daily', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['code'] != 200) {
            return FALSE;
        }else{

            $data = $result['content']['data'];
            foreach ($data as $key => $value) {
                $weather = $value['weather'];
                $weather['skycon'] = self::getWeather($weather);
                $data[$key]['weather'] = $weather;
            }

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);

            return $data;
        }
    }

    /**
     * 查询城市
     */
    public static function getHourlyWeather($params = array()) {
        //查询缓存
        $cacheKey = 'Weather:getHourlyWeather:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'weather/get_hourly', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['code'] != 200) {
            return FALSE;
        }else{

            $weather = $result['content']['data']['weather'];
            $sys = $result['content']['data']['sys'];
            $weather['skycon'] = self::getWeather($weather, $sys);
            $result['content']['data']['weather'] = $weather;

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $result['content']['data'], self::$expireTime);

            return $result['content']['data'];
        }
    }

    /**
     * 匹配skycons图标
     * http://openweathermap.org/weather-conditions
     */
    public static function getWeather($weather = array(), $sys = array()) {

        $weatherid = $weather['id'];
        $postfix = empty($sys) ? self::getDayOtNight($sys) : '_DAY';

        if ($weatherid < 300) {
            $sky = 'SLEET';
        } elseif ($weatherid < 400) {
            $sky = 'SLEET';
        } elseif ($weatherid < 600) {
            $sky = 'RAIN';
        } elseif ($weatherid < 700) {
            $sky = 'SNOW';
        } elseif ($weatherid < 800) {
            $sky = 'FOG';
        } elseif ($weatherid < 900) {
            if ($weatherid < 802) {
                $sky = 'CLEAR'.$postfix;
            }else{
                $sky = 'PARTLY_CLOUDY'.$postfix;
            }
        } elseif ($weatherid < 950) {
            $sky = 'WIND';
        } else {
            $sky = 'WIND';
        }

        return $sky;
    }

    /**
     * 匹配skycons图标
     */
    public static function getDayOtNight($sys = array()) {
        if (empty($sys) || empty($sys['sunrise']) || empty($sys['sunset'])) {
            return '_DAY';
        }

        $now = time();
        if ($now > $sys['sunrise'] && $now < $sys['sunset']) {
            return '_DAY';
        }else{
            return '_NIGHT';
        }
    }

} 
