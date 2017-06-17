<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 请假信息
 * @author minggeng@meilishuo.com
 * @since 2015-12-16
 */
class OrderOperateQueue extends BaseQuery{

    private static $pk = 'id';

    private static $fields = array(
        'id'            => 0,
        'order_id'      => 0,
        'task_id'       => 0,
        'order_type'    => 1,
        'operate_type'  => 1,
        'handle_user_id'=> 0,
        'task_name'     => '',
        'action_type'   => 1,
        'progress_content'=> '',
        'task_content'  => '',
        'status'        => 0,
        'update_time'   =>'0000-00-00 00:00:00',
        'create_time'   => '0000-00-00 00:00:00',
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_operate_queue';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\OrderOperateQueue
     */
    public static function model(){
        return parent::model();
    }

    /**
     * 查询
     * @param $data
     * @param array $params
     * @return array
     */
    public function getList(array $params = array(),$offset = 0,$limit = 100){
        $qb = $this->builder();
        //order_id
        if (!empty($params['order_id'])) {
            if (is_array($params['order_id'])) {
                $qb->whereIn('order_id', $params['order_id']);
            }else{
                $qb->where('order_id', '=', $params['order_id']);
            }
        }

        //task_id
        if (!empty($params['task_id'])) {
            if (is_array($params['task_id'])) {
                $qb->whereIn('task_id', $params['task_id']);
            }else{
                $qb->where('task_id', '=', $params['task_id']);
            }
        }

        //order_type
        if (!empty($params['order_type'])) {
            if (is_array($params['order_type'])) {
                $qb->whereIn('order_type', $params['order_type']);
            }else{
                $qb->where('order_type', '=', $params['order_type']);
            }
        }

        //status
        if (isset($params['status'])) {
            $qb->where('status', '=', $params['status']);
        }

        //operate_type
        if(!empty($params['operate_type'])){
            if (is_array($params['operate_type'])) {
                $qb->whereIn('operate_type', $params['operate_type']);
            }else{
                $qb->where('operate_type', '=', $params['operate_type']);
            }
        }

        //handle_user_id
        if(!empty($params['handle_user_id'])){
            if (is_array($params['handle_user_id'])) {
                $qb->whereIn('handle_user_id', $params['handle_user_id']);
            }else{
                $qb->where('handle_user_id', '=', $params['handle_user_id']);
            }
        }

        //TODO 根据查询参数params build sql.
        $ret = $qb->offset($offset)->limit($limit)->get();
        return $ret;
    }

}
