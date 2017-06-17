<?php
namespace Atom\Package\Company;

/**
 * 城市
 * @package Atom\Package\Company
 * @author hepang@meilishuo.com
 * @since 2015-04-07
 */

use Atom\Package\Common\DbAdapter;

class City {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'city';
    private static $col = array('city_id', 'city_name', 'status',);
    private static $pk = 'city_id';
    private static $fields = array(
            'city_id'       => 0,
            'city_name'     => '',
            'status'        => 1 ,//状态:0无效1有效
    );
    private static $update_fields = array(
        'city_name'     => 'string',
        'status'        => 'int', 
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
    public function getCityById($id, $fields = array())
    {
        if (empty($fields)) {
            $fields = static::$col;
        }

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col)
            ->find($id, static::$pk);

        return $ret;
    }

    /**
     * 查询列表
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getCityList(array $params = array(), $page = 1, $pageSize = 20)
    {
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);
            //分页
        $page = $page < 1 ? 1: $page;


        //查询条件
        if (isset($params['city_id'])) {
            if (is_array($params['city_id'])) {
                $ret->whereIn('city_id', $params['city_id']);
            }else{
                $ret->where('city_id', '=', $params['city_id']);
            }
        }
        if (isset($params['city_name'])) {
            $ret->where('city_name', 'LIKE', '%'.$params['city_name'].'%');
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }

        if(isset($params['count'])&&$params['count']==1){
            $ret->hash(self::$pk);
            return $ret->count();
        }
        if(isset($params['all'])&&$params['all']!=1){
            $ret->hash(self::$pk);
            return $ret->get();
        }
        //$queryObj = $ret->getQuery();
        $ret->hash(self::$pk)->select(static::$col)
            ->offset(($page-1)*$pageSize)
            ->limit($pageSize);;

        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['city_name'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        return $ret->insert($params);
    }

    /**
     * 更新
     */
    public static function update($params) {

        if (!isset($params[self::$pk])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[self::$pk];
        unset($params[self::$pk]);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        $ret = $ret->where(self::$pk, $pkid);
        return $ret->update($params);
    }














} 
