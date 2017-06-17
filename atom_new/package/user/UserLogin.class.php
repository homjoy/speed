<?php
namespace Atom\Package\User;

/**
 * 会议室
 * @package Atom\Package\User
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

use Atom\Package\Common\DbAdapter;

class UserLogin {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'user_login';
    private static $col = array('id', 'user_id', 'ip', 'salt', 'password', 'session', 'expire_time', 'status',);
    private static $pk = 'id';
    private static $fields = array(
            'id'            => 0,
            'user_id'       => 0,
            'ip'            => 0,
            'salt'          => '',
            'password'      => '',
            'session'       => '',
            'expire_time'   => 0,
            'status'        => 0,//状态:0无效1有效
    );
    private static $update_fields = array(
        'user_id'       => 'int',
        'ip'            => 'int',
        'salt'          => 'string',
        'password'      => 'string',
        'session'       => 'string',
        'expire_time'   => 'int',
        'ctime'         => 'string',
        'status'        => 'int',
    );

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
            self::$conn =  new \Pixie\Connection(new DbAdapter('core'));
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
    public function getDataById($id, $fields = array())
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
    public function getDataList(array $params = array(), $page = 1, $pageSize = 20)
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
        if (isset($params['id'])) {
            if (is_array($params['id'])) {
                $ret->whereIn('id', $params['id']);
            }else{
                $ret->where('id', '=', $params['id']);
            }
        }
        if (isset($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $ret->whereIn('user_id', $params['user_id']);
            }else{
                $ret->where('user_id', '=', $params['user_id']);
            }
        }
        if (isset($params['session'])) {
            if (is_array($params['session'])) {
                $ret->whereIn('session', $params['session']);
            }else{
                $ret->where('session', '=', $params['session']);
            }
        }
        if (isset($params['expire_time'])) {
            $ret->where('expire_time', '<', $params['expire_time']);
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        //$queryObj = $ret->getQuery();
        //$s = $queryObj->getSql();
        //var_dump($s);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['user_id']) || !isset($params['ip']) || !isset($params['session']) || !isset($params['expire_time'])){
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
