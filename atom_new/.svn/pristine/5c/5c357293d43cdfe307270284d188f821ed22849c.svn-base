<?php
namespace Atom\Package\Company;

/**
 * 公司
 * @package Atom\Package\Company
 * @author hepang@meilishuo.com
 * @since 2015-04-10
 */

use Atom\Package\Common\DbAdapter;

class Company {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'company';
    private static $col = array('company_id', 'city_id', 'company_name', 'company_address', 'company_addr', 'telephone', 'fax', 'status',);
    private static $pk = 'company_id';
    private static $fields = array(
            'company_id'    => 0,
            'city_id'       => 0,
            'company_name'  => '',
            'company_address'  => '',
            'company_addr'  => '',
            'telephone'     => '',
            'fax'           => '',
            'status'        => 1 ,//状态:0无效1有效
    );
    private static $update_fields = array(
        'city_id'       => 'int',
        'company_name'  => 'string',
        'company_address'  => 'string',
        'company_addr'  => 'string',
        'telephone'     => 'string',
        'fax'           => 'string',
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
    public function getCompanyById($id, $fields = array())
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
    public function getCompanyList(array $params = array(), $page = 1, $pageSize = 20)
    {
        //分页
        $page = $page < 1 ? 1: $page;
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);
        //查询


        //查询条件
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
        if (isset($params['company_name'])) {
            $ret->where('company_name', 'LIKE', '%'.$params['company_name'].'%');
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

        $ret->hash(self::$pk)
            ->offset(($page-1)*$pageSize)
            ->limit($pageSize);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['company_name']) || !isset($params['city_id'])){
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
