<?php
namespace Atom\Package\Mail;

use Atom\Package\Common\DbAdapter;

/**
 *
 * Class MailGroupUser
 * @package Atom\Package\Weather
 *
 */

class MailGroupUser{

    private static $instance = null;
    private static $conn;

    private static $tableName = 'mail_group_user';
    private static $col = array('id', 'group_id', 'user_id', 'status', 'user_mail','mail_suffix_type');
    private static $pk = 'id';
    private static $fields = array(
        'id'        => 0,
        'group_id'  => 0,
        'user_id'   => 0,
        'status'    => 1,
        'user_mail' => '',
        'mail_suffix_type' => 1
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
        if (empty($params)) {
            return FALSE;
        }
        if (!isset($params['status'])) {
            $params['status'] = 1;
        }

        //查询
        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName)
            ->select(static::$col);


        //group_id
        if (!empty($params['group_id'])) {
            if (is_array($params['group_id'])) {
                $ret->whereIn('group_id', $params['group_id']);
            }else{
                $ret->where('group_id', '=', $params['group_id']);
            }
        }

        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $ret->whereIn('user_id', $params['user_id']);
            }else{
                $ret->where('user_id', '=', $params['user_id']);
            }
        }

        //邮件组名称
        if (!empty($params['user_mail'])) {

            if (is_array($params['user_mail'])) {
                $ret->whereIn('user_mail', $params['user_mail']);
            }else{
                $ret->where('user_mail', 'like', $params['user_mail'].'%');
            }
        }
        //状态
        if (is_array($params['status'])) {
            $ret->whereIn('status', $params['status']);
        }else{
            $ret->where('status', '=', $params['status']);
        }
        //类别
        if(!empty($params['mail_suffix_type'])) {
            if (is_array($params['mail_suffix_type'])) {
                $ret->whereIn('mail_suffix_type', $params['mail_suffix_type']);
            } else {
                $ret->where('mail_suffix_type', '=', $params['mail_suffix_type']);
            }
        }
        $ret->orderBy(self::$pk,'asc');

//        $ret->hash(self::$pk);
//        $queryObj = $ret->getQuery();
//        echo $queryObj->getRawSql();
//    exit;
        //$ret->hash(self::$pk);
        //获取总页数
        if(isset($params['count']) && $params['count'] == 1){
            return $ret->count();
        }
        if(isset($params['all']) && $params['all'] == 1){
            return $ret->get();
        }
        $ret ->offset(($page-1)*$pageSize)->limit($pageSize);
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['group_id']) || !isset($params['user_id'])){
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

        if ( !empty($params[self::$pk]) ||  !empty($params['group_id']) || !empty($params['user_id'])  ){
            # go on
        }else{
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);

        if (!empty($params[self::$pk])) {            
            $ret = $ret->where(self::$pk, $params[self::$pk]);
            unset($params[self::$pk]);
        }else if(isset($params[self::$pk])){
            unset($params[self::$pk]);
        }
        if (!empty($params['group_id'])) {
            $ret = $ret->where('group_id', $params['group_id']);
            unset($params['group_id']);
        }else if(isset($params['group_id'])){
            unset($params['group_id']);
        }
        if (!empty($params['user_id'])) {
            $ret = $ret->where('user_id', $params['user_id']);
            unset($params['user_id']);
        }else if(isset($params['user_id'])){
            unset($params['user_id']);
        }

        return $ret->update($params);
    }
    /**
     * 根据 groupid 更新 邮件组的列表
     * add guojeizhu
     */
    public static function updateByGroup($params) {

        if ( empty($params['group_id'])  ){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);

        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);

        if (!empty($params[self::$pk])) {
            $ret = $ret->where(self::$pk, $params[self::$pk]);
            unset($params[self::$pk]);
        }
        if (!empty($params['group_id'])) {
            $ret = $ret->where('group_id', $params['group_id']);
            unset($params['group_id']);
        }
        return $ret->update($params);
    }
}