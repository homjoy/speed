<?php
namespace Worker\Package\Weather;

use Worker\Package\Common\BaseQuery;

/**
 *
 * Class WeatherHourly
 * @package Worker\Package\Weather
 *
 */
class WeatherToday extends BaseQuery{

    /**
     * @return string
     */
    public static function database()
    {
        return 'administration';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'weather_today';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'        => 0,
        'city_id'   => 0,
        'date'      => '',
        'data'      => '',
        'update_time'   => '',
    );

    /**
     * @param $params
     * @return array|string
     */
    public function create($params)
    {
        if(empty($params['city_id']) || empty($params['date']) || empty($params['data'])){
            return 0;
        }
        $data = array_intersect_key($params, static::$fields);
        $data = array_merge(static::$fields, $data);
        unset($params[static::pk()]);
        unset($data['update_time']);

        return $this->builder()->insert($data);
    }

    /**
     * @param $params
     * @return $this|int
     */
    public function update($params)
    {
        if(!isset($params[static::pk()]) || empty($params[static::pk()])){
            return 0;
        }
        $pk = $params[static::pk()];
        $data = array_intersect_key($params, static::$fields);
        unset($params[static::pk()]);
        unset($params['update_time']);

        return $this->builder()->where(static::pk(),$pk)->update($data);
    }

    /**
     * @param $city_id
     * @param $date
     * @return array|null|\stdClass
     */
    public function getData($city_id, $date)
    {
        if(empty($city_id) || empty($date)){
            return array();
        }

        $builder = $this->builder()->where('city_id', $city_id);
        return $builder->where('date', $date)->first();
    }


}