<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 请假信息
 * @author minggeng@meilishuo.com
 * @since 2016-2-29
 */
class OrderAssetsDetail extends BaseQuery{

    private static $pk = 'id';

    private static $fields = array(
        'id'            => 0,
        'order_id'      => 0,
        'type'          => 1,
        'standard'      => '',
        'assets_name'   => '',
        'assets_brand'  => '',
        'assets_number' => 0,
        'detail_reason' => '',
        'status'        => 1,
        'update_time'   =>'0000-00-00 00:00:00',
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_assets_detail';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\OrderAssetsDetail
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

        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $qb->whereIn('id', $params['id']);
            }else{
                $qb->where('id', '=', $params['id']);
            }
        }

        //standard
        if (!empty($params['standard'])) {
            if (is_array($params['standard'])) {
                $qb->whereIn('standard', $params['standard']);
            }else{
                $qb->where('standard', '=', $params['standard']);
            }
        }

        //status
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $qb->whereIn('status', $params['status']);
            }else{
                $qb->where('status', '=', $params['status']);
            }
        }else{
            $qb->where('status', '=', 1);
        }

        //type
        if (!empty($params['type'])) {
            if (is_array($params['type'])) {
                $qb->whereIn('type', $params['type']);
            }else{
                $qb->where('type', '=', $params['type']);
            }
        }

        //TODO 根据查询参数params build sql.
        $ret = $qb->offset($offset)->limit($limit)->get();
        return $ret;
    }

}
