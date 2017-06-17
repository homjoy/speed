<?php
namespace Atom\Package\Approval;

/**
 * 邮件详细信息
 * @author haibinzhou@meilishuo.com
 * @since 2015-11-23
 */

use Atom\Package\Common\BaseQuery;

class OrderExpressDetail extends BaseQuery{

    private static $col = array('id', 'order_id', 'tracking_id', 'update_time', 'mail_weight','status');
    private static $pk = 'id';
    private static $fields = array(
        'id'                 => 0,
        'order_id'           => 0,
        'tracking_id'        => 0,
        'mail_weight'        => '',
        'status'             => 1,
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_express_detail';
    }

    public static function pk(){
        return 'id';
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * 查询列表
     */
    public function getDataList($params = array(),$offset = 0,$limit = 100)
    {

        $builder = $this->builder();

        //查询条件
        if (isset($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }


        if (isset($params['order_id'])) {
            if (is_array($params['order_id'])) {
                $builder->whereIn('order_id', $params['order_id']);
            }else{
                $builder->where('order_id', '=', $params['order_id']);
            }
        }

        if (isset($params['status'])) {      //状态
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }else{
            $builder->where('status', '=', 1);
        }

        if (isset($params['tracking_id'])) {
            if (is_array($params['tracking_id'])) {
                $builder->whereIn('tracking_id', $params['tracking_id']);
            }else{
                $builder->where('tracking_id', '=', $params['tracking_id']);
            }
        }

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }


        $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('id','DESC');

       /* $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();die; */
        $builder->hash(self::$pk);

        return $builder->get();
    }

    /**
     * 更新
     */
    public function update($params) {
        if (!isset($params[static::pk()])){
            return false;
        }

        $builder = $this->builder();
        $pkid = $params[static::pk()];
        unset($params[static::pk()]);
        $builder->where(static::pk(), $pkid);

        return $builder->update($params);
    }

    public function insert($params) {
        $builder = $this->builder();

        if (!isset($params['order_id']) || !isset($params['tracking_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

}
