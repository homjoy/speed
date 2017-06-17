<?php
namespace Atom\Package\Company;

/**
 * 办公室
 * @package Atom\Package\Company
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

use Atom\Package\Common\DbAdapter;

class Office {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'office';
    private static $col = array('office_id', 'city_id', 'company_id', 'office_floor', 'office_name', 'office_position', 'office_address','office_telephone','office_fax','office_capacity', 'office_detail', 'status',);
    private static $pk = 'office_id';
    private static $fields = array(
        'office_id' => 0,
        'city_id' => 0,
        'company_id' => 0,
        'office_floor' => 0,
        'office_name' => '',
        'office_address' => '',
        'office_telephone' => '',
        'office_fax' => '',
        'office_position' => '',
        'office_capacity' => '',
        'office_detail' => '',
        'status' => 1,//状态:0无效1有效
    );
    private static $update_fields = array(
        'city_id'       => 'int',
        'company_id'    => 'int',
        'office_floor'  => 'int',
        'office_name'   => 'string',
        'office_position'   => 'string',
        'office_capacity'   => 'string',
        'office_address' => 'string',
        'office_telephone' => 'string',
        'office_fax' => 'string',
        'office_detail' => 'string',
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
    public function getOfficeById($id, $fields = array())
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
     */
    public function getOfficeList(array $params = array(), $page = 1, $pageSize = 20)
    {
        //分页
        $page = $page < 1 ? 1: $page;

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col)
            ->offset(($page-1)*$pageSize)
            ->limit($pageSize);

        //查询条件
        if (isset($params['office_id'])) {
            if (is_array($params['office_id'])) {
                $ret->whereIn('office_id', $params['office_id']);
            }else{
                $ret->where('office_id', '=', $params['office_id']);
            }
        }
        if (isset($params['company_id'])) {
            if (is_array($params['company_id'])) {
                $ret->whereIn('company_id', $params['company_id']);
            }else{
                $ret->where('company_id', '=', $params['company_id']);
            }
        }
        if (isset($params['city_id'])) {
            if (is_array($params['city_id'])) {
                $ret->whereIn('city_id', $params['city_id']);
            }else{
                $ret->where('city_id', '=', $params['city_id']);
            }
        }
        if (isset($params['office_name'])) {
            $ret->where('office_name', 'LIKE', '%'.$params['office_name'].'%');
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }

        $ret->hash(self::$pk);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['office_name']) || !isset($params['company_id']) || !isset($params['city_id'])){
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
