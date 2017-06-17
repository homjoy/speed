<?php
namespace Worker\Package\Weather;

use Worker\Package\Common\BaseQuery;

/**
 *
 * Class City
 * @package Worker\Package\Weather
 *
 */
class City extends BaseQuery{

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
        return 'city';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'city_id';
    }

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'city_id'   => 0,
        'city_name' => '',
        'status'    => 0,
        'pinyin'    => '',
        'weather_id'    => 0,
    );

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

        return $this->builder()->where(static::pk(),$pk)->update($data);
    }

    /**
     * @param $city_id
     * @return array|null|\stdClass
     */
    public function getDataById($city_id=0)
    {
        if(empty($city_id)){
            return array();
        }

        $builder = $this->builder()->where('city_id', $city_id);
        return $builder->first();
    }

    /**
     * @return array|null|\stdClass
     */
    public function getAllData()
    {
        return $this->builder()->get();
    }

}