<?php
namespace Atom\Package\Meeting;

/**
 * 会议室设备关系
 * @package Atom\Package\Company
 * @author hepang@meilishuo.com
 * @since 2015-04-15
 */

use Atom\Package\Common\DbAdapter;

class MeetingEquipmentRelation {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'meeting_equipment_relation';
    private static $col = array('id', 'room_id', 'equipment_id', 'status',);
    private static $pk = 'id';
    private static $fields = array(
            'id'        => 0,
            'room_id'   => 0,
            'equipment_id'  => 0,
            'status'    => 0,//状态:0无效1有效
    );
    private static $update_fields = array(
        'room_id'   => 'int',
        'equipment_id'  => 'int',
        'status'    => 'int',
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
        if (isset($params['id'])) {
            if (is_array($params['id'])) {
                $ret->whereIn('id', $params['id']);
            }else{
                $ret->where('id', '=', $params['id']);
            }
        }
        if (isset($params['room_id'])) {
            if (is_array($params['room_id'])) {
                $ret->whereIn('room_id', $params['room_id']);
            }else{
                $ret->where('room_id', '=', $params['room_id']);
            }
        }
        if (isset($params['equipment_id'])) {
            if (is_array($params['equipment_id'])) {
                $ret->whereIn('equipment_id', $params['equipment_id']);
            }else{
                $ret->where('equipment_id', '=', $params['equipment_id']);
            }
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }

        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['room_id']) || !isset($params['equipment_id'])){
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
