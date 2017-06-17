<?php
namespace Atom\Package\Message_send;

/**
 * 信息发送人员
 * guojiezhu@meilishuo.com
 *
 */

use Atom\Package\Common\DbAdapter;

class MessageUserList{

    private static $instance = null;
    private static $conn;

    private static $tableName = 'message_user_list';
    private static $col = array('user_list_id',
                                'msg_id',
                                'name_cn',
                                'to_id',
                                'mail',
                                'phone',
                                'status',
                                'update_time');
    private static $pk = 'user_list_id';
    private static $fields = array(
        'user_list_id'      =>0,
        'msg_id'            =>0,
        'name_cn'           =>'',
        'to_id'             =>0,
        'mail'              =>'',
        'phone'             =>'',
        'status'            =>0,
    );
    private static $update_fields = array(
        'user_list_id'      =>'int',
        'msg_id'            =>'int',
        'name_cn'           =>'string',
        'to_id'             =>'int',
        'mail'              =>'string',
        'phone'             =>'string',
        'status'            =>'int',
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
            ->select(static::$col);

        //查询条件
        if (isset($params['msg_id'])) {
            if (is_array($params['msg_id'])) {
                $ret->whereIn('msg_id', $params['msg_id']);
            }else{
                $ret->where('msg_id', '=', $params['msg_id']);
            }
        }
        if (isset($params['to_id'])) {
            if (is_array($params['to_id'])) {
                $ret->whereIn('to_id', $params['to_id']);
            }else{
                $ret->where('to_id', '=', $params['to_id']);
            }
        }

        if (isset($params['name_cn'])) {
            $ret->where('name_cn', 'LIKE', '%'.$params['name_cn'].'%');
        }
        if (isset($params['phone'])) {
            $ret->where('phone', 'LIKE', '%'.$params['phone'].'%');
        }
        if (isset($params['mail'])) {
            $ret->where('mail', 'LIKE', '%'.$params['mail'].'%');
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $ret->count();
        }

        if(isset($params['all']) && $params['all'] == 1){
            return $ret->hash(static::$pk)->get();
        }

        $ret->hash(self::$pk)->offset(($page-1)*$pageSize)
            ->limit($pageSize);
        $ret->orderBy(static::$pk,'desc');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['msg_id']) ){
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
        if (is_array($pkid)) {
            $ret->whereIn('msg_id', $pkid);
        }else{
            $ret->where('msg_id', '=', $pkid);
        }
        return $ret->update($params);
    }
    /**
     * 删除（谨慎使用）
     */
    public function deleteByMsgId($params){


        $qb = self::$conn->getQueryBuilder();
        $ret = $qb->table(static::$tableName);
           if(!isset($params['msg_id'])){
               return FALSE;
           }
        if (is_array($params['msg_id'])) {
            $ret->whereIn('msg_id', $params['msg_id']);
        }else{
            $ret->where('msg_id', '=', $params['msg_id']);
        }
        return $ret->delete($params);
    }
} 
