<?php
namespace Atom\Package\Mail;

use Atom\Package\Common\DbAdapter;

/**
 *
 * Class MailGroup
 * @package Atom\Package\Weather
 *
 */

class MailGroup{

    private static $instance = null;
    private static $conn;

    private static $tableName = 'mail_group';
    private static $col = array('group_id', 'group_name', 'status','memo');
    private static $pk = 'group_id';
    private static $fields = array(
        'group_id'  => 0,
        'group_name'    => '',
        'status'        => 1,
        'memo'          => ''
    );
    private static $update_fields = array(
        'status'    => 0,
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
    public function getDataList($params = array(), $page = 1, $pageSize = 20)
    {
        if (!is_array($params)) {
            return FALSE;
        }

        if (!isset($params['status'])) {
            $params['status'] = 1;
        }

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);


        //id
        if (!empty($params['group_id'])) {
            if (is_array($params['group_id'])) {
                $ret->whereIn('group_id', $params['group_id']);
            }else{
                $ret->where('group_id', '=', $params['group_id']);
            }
        }

        //邮件组名称
        if (!empty($params['group_name'])) {

            if (is_array($params['group_name'])) {
                $ret->whereIn('group_name', $params['group_name']);
            }else{

                if (empty($params['strict'])) {
                    $ret->where('group_name', 'like', $params['group_name'].'%');
                }else{
                    $ret->where('group_name', '=', $params['group_name']);
                }

            }
        }

        //状态
        if (is_array($params['status'])) {
            $ret->whereIn('status', $params['status']);
        }else{
            $ret->where('status', '=', $params['status']);
        }


        $ret->orderBy(self::$pk,'asc');
        if(isset($params['count']) && $params['count'] == 1){
            return $ret->count();
        }
        //$ret->hash(self::$pk);
        //$queryObj = $ret->getQuery();
        //echo $queryObj->getRawSql();
        $ret->offset(($page-1)*$pageSize)
            ->limit($pageSize);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['group_name'])){
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

        if (!isset($params[self::$pk]) && !isset($params['group_name'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);

        if (!empty($params[self::$pk])) {
            $ret = $ret->where(self::$pk, $params[self::$pk]);
            unset($params[self::$pk]);
        }
        if (!empty($params['group_name'])) {
            $ret = $ret->where('group_name', $params['group_name']);
            unset($params['group_name']);
        }

        unset($params[self::$pk]);
        return $ret->update($params);
    }

}