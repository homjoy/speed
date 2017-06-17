<?php
namespace Atom\Package\Message_send;

/**
 * 信息批量发送平台
 * guojiezhu@meilishuo.com
 *
 */

use Atom\Package\Common\DbAdapter;

class MessageList {

    private static $instance = null;
    private static $conn;

    private static $tableName = 'message_list';
    private static $col = array('msg_id', 'send_object', 'channel', 'template_id', 'weights', 'title','content', 'send_at', 'status', 'send_status','update_time','op_user_id');
    private static $pk = 'msg_id';
    private static $fields = array(
            'msg_id'       =>0,
            'send_object'  =>0,
            'channel'      =>0,
            'template_id'  =>0,
            'weights'      =>0,
            'title'        => '',
            'content'	   => '',
            'send_at'	   => '',
            'status'	   => 0,
            'send_status'  => 0,
            'op_user_id'   =>0,
    );
    private static $update_fields = array(
        	'msg_id'       =>'int',
            'send_object'  =>'int',
            'channel' 	   =>'int',
            'template_id'  =>'int',
            'weights' 	   =>'int',
            'title'        =>'string',
            'content'	   =>'string',
            'send_at'      =>'string',
            'status'       =>'int',
            'send_status'  =>'int',
            'op_user_id'   =>'int',
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
        if (isset($params['send_object'])) {
            if (is_array($params['send_object'])) {
                $ret->whereIn('send_object', $params['send_object']);
            }else{
                $ret->where('send_object', '=', $params['send_object']);
            }
        }
        if (isset($params['channel'])) {
            if (is_array($params['channel'])) {
                $ret->whereIn('channel', $params['channel']);
            }else{
                $ret->where('channel', '=', $params['channel']);
            }
        }
        if (isset($params['template_id'])) {
            if (is_array($params['template_id'])) {
                $ret->whereIn('template_id', $params['template_id']);
            }else{
                $ret->where('template_id', '=', $params['template_id']);
            }
        }
        if (isset($params['title'])) {
            $ret->where('title', 'LIKE', '%'.$params['title'].'%');
        }
        if(isset($params['send_at_start'])|| isset($params['send_at_end'])){
            $ret->where(function($ret)use($params){
                if (!empty($params['send_at_start'])  && !empty($params['send_at_end'])){
                    $ret->whereBetween('send_at',$params['send_at_start'],$params['send_at_end']);
                }  elseif (!empty($params['send_at_end'])){
                    $ret->where('send_at','<=', $params['send_at_end']);
                }  elseif(!empty($params['send_at_start'])) {
                    $ret->where('send_at','>=', $params['send_at_start']);
                }

            });
        }
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $ret->whereIn('status', $params['status']);
            }else{
                $ret->where('status', '=', $params['status']);
            }
        }
        if (isset($params['send_status'])) {
            if (is_array($params['send_status'])) {
                $ret->whereIn('send_status', $params['send_status']);
            }else{
                $ret->where('send_status', '=', $params['send_status']);
            }
        }
        if (isset($params['op_user_id'])) {
            if (is_array($params['op_user_id'])) {
                $ret->whereIn('op_user_id', $params['op_user_id']);
            }else{
                $ret->where('op_user_id', '=', $params['op_user_id']);
            }
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $ret->count();
        }
        if(isset($params['all']) && $params['all'] == 1){
            return $ret->hash(static::$pk)->get();
        }

//        $ret->hash(static::$pk);
//        $queryObj = $ret->getQuery();
//        echo $queryObj->getRawSql();
//        exit;

        $ret->hash(static::$pk)->offset(($page-1)*$pageSize)
            ->limit($pageSize);
        $ret->orderBy(static::$pk,'desc');
        return $ret->get();
    }

    /**
     * 添加
     */
    public static function insert($params) {

        if (!isset($params['send_object']) || !isset($params['channel']) || !isset($params['template_id']) || !isset($params['send_at'])){
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

} 
