<?php
namespace Atom\Package\Weather;

use Atom\Package\Common\DbAdapter;

/**
 *
 * Class WeatherHourly
 * @package Atom\Package\Weather
 *
 */

class WeatherHourly{

    private static $instance = null;
    private static $conn;

    private static $tableName = 'weather_hourly';
    private static $col = array('id', 'city_id', 'timestamp', 'data', 'update_time',);
    private static $pk = 'id';
    private static $fields = array(
        'id'        => 0,
        'city_id'   => 0,
        'timestamp' => '',
        'data'      => '',
        'update_time'   => '',
    );
    private static $update_fields = array(
        'data'      => '',
    );

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
            self::$conn =  new \Pixie\Connection(new DbAdapter('administration'));
        }
        return self::$instance;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 获取单条信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $limit = 1)
    {
        if (empty($params) || empty($params['city_id']) || empty($params['timestamp'])) {
            return FALSE;
        }

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col)
            ->limit($limit);

        //查询条件
        if (is_array($params['city_id'])) {
            $ret->whereIn('city_id', $params['city_id']);
        }else{
            $ret->where('city_id', '=', $params['city_id']);
        }

        $ret->where('timestamp', '<', $params['timestamp']);
        $ret->orderBy(self::$pk,'desc');
        $ret->limit($limit);

        //$ret->hash(self::$pk);
        return $ret->get();
    }

}