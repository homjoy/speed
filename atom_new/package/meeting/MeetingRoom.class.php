<?php
namespace Atom\Package\Meeting;

/**
 * 会议室
 * @package Atom\Package\Company
 * @author hepang@meilishuo.com
 * @since 2015-04-13
 */

use Atom\Package\Common\DbAdapter;

class MeetingRoom {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'meeting_room';
    private static $col = array('room_id', 'city_id', 'company_id', 'office_id', 'room_sn', 'room_name', 'room_position', 'room_capacity', 'extension', 'status', 'room_detail', 'type',);
    private static $pk = 'room_id';
    private static $fields = array(
            'room_id'       => 0,
            'city_id'       => 0,
            'company_id'    => 0,
            'office_id'     => 0,
            'room_sn'       => '',
            'room_name'     => '',
            'room_position' => '',
            'room_capacity' => 0,
            'extension'     => 0,
            'status'        => 0,//状态:0无效1有效
            'room_detail'   => '',
            'type'          => 0,//类型:1会议室2活动场地
    );
    private static $update_fields = array(
        'city_id'       => 'int',
        'company_id'    => 'int',
        'office_id'     => 'int',
        'room_sn'       => 'string',
        'room_name'     => 'string',
        'room_position' => 'string',
        'room_capacity' => 'int',
        'extension'     => 'int',
        'status'        => 'int',
        'room_detail'   => 'string',
        'type'          => 'int',
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
        if (isset($params['room_id'])) {
            if (is_array($params['room_id'])) {
                $ret->whereIn('room_id', $params['room_id']);
            }else{
                $ret->where('room_id', '=', $params['room_id']);
            }
        }
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
        if (isset($params['room_name'])) {
            $ret->where('room_name', 'LIKE', '%'.$params['room_name'].'%');
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if(isset($params['count']) && $params['count'] == 1){
            return  $ret->count();
        }
        $ret->hash(self::$pk);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['room_name']) || !isset($params['company_id']) || !isset($params['city_id']) || !isset($params['office_id'])){
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


    /**
     * 数据迁移用的插入，不重置主键.
     * @param $data
     * @return mixed
     */
    public static function migrateInsert($data)
    {
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
        return $ret->insert($data);
    }
} 
