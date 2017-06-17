<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 请假信息
 * @author minggeng@meilishuo.com
 * @since 2016-2-29
 */
class OrderAssetsParity extends BaseQuery{

    private static $pk = 'id';

    private static $fields = array(
        'id'            => 0,
        'detail_id'     => 0,
        'company_id'    => 0,
        'equipment_id'  => 0,
        'class_id'      => 0,
        'brand_id'      => 0,
        'model_id'      => 0,
        'price'         => 0,
        'selected'      => 0,
        'status'        => 1,
        'update_time'   =>'0000-00-00 00:00:00',
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_assets_parity';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\OrderAssetsParity
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
        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $qb->whereIn('id', $params['id']);
            }else{
                $qb->where('id', '=', $params['id']);
            }
        }
        //detail_id
        if (!empty($params['detail_id'])) {
            if (is_array($params['detail_id'])) {
                $qb->whereIn('detail_id', $params['detail_id']);
            }else{
                $qb->where('detail_id', '=', $params['detail_id']);
            }
        }

        //company_id
        if (!empty($params['company_id'])) {
            if (is_array($params['company_id'])) {
                $qb->whereIn('company_id', $params['company_id']);
            }else{
                $qb->where('company_id', '=', $params['company_id']);
            }
        }

        //equipment_id
        if (!empty($params['equipment_id'])) {
            if (is_array($params['equipment_id'])) {
                $qb->whereIn('equipment_id', $params['equipment_id']);
            }else{
                $qb->where('equipment_id', '=', $params['equipment_id']);
            }
        }

        //class_id
        if (!empty($params['class_id'])) {
            if (is_array($params['class_id'])) {
                $qb->whereIn('class_id', $params['class_id']);
            }else{
                $qb->where('class_id', '=', $params['class_id']);
            }
        }

        //brand_id
        if (!empty($params['brand_id'])) {
            if (is_array($params['brand_id'])) {
                $qb->whereIn('brand_id', $params['brand_id']);
            }else{
                $qb->where('brand_id', '=', $params['brand_id']);
            }
        }

        //model_id
        if (!empty($params['model_id'])) {
            if (is_array($params['model_id'])) {
                $qb->whereIn('model_id', $params['model_id']);
            }else{
                $qb->where('model_id', '=', $params['model_id']);
            }
        }


        //status
        if (isset($params['status'])) {
            $qb->where('status', '=', $params['status']);
        }

        //selected
        if(!empty($params['selected'])){
            if (is_array($params['selected'])) {
                $qb->whereIn('selected', $params['selected']);
            }else{
                $qb->where('selected', '=', $params['selected']);
            }
        }

        //TODO 根据查询参数params build sql.
        $ret = $qb->offset($offset)->limit($limit)->get();
        return $ret;
    }

}
