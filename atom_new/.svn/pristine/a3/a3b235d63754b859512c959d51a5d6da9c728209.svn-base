<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 请假信息
 * @author minggeng@meilishuo.com
 * @since 2015-10-30
 */
class OrderSendQueue extends BaseQuery{

    private static $pk = 'id';

    private static $fields = array(
        'id'        => 0,
        'order_id'  => 0,
        'order_type'=> '',
        'send_type' => '',
        'handle_user_id' => 0,
        'status'    => 0,
        'update_time'=>'0000-00-00 00:00:00',
        'send_at'  => '0000-00-00 00:00:00',
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_send_queue';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\OrderSendQueue
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
    public function getList(array $params = array()){
        $qb = $this->builder();
        //order_id
        if (!empty($params['order_id'])) {
            if (is_array($params['order_id'])) {
                $qb->whereIn('order_id', $params['order_id']);
            }else{
                $qb->where('order_id', '=', $params['order_id']);
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

        //send_type
        if (!empty($params['send_type'])) {
            if (is_array($params['send_type'])) {
                $qb->whereIn('send_type', $params['send_type']);
            }else{
                $qb->where('send_type', '=', $params['send_type']);
            }
        }

        //时间判定
        if(!empty($params['send_at'])){
            $qb->where('send_at','LIKE', "%{$params['send_at']}%");
        }

        //TODO 根据查询参数params build sql.
        $ret = $qb->get();
        return $ret;
    }

}
