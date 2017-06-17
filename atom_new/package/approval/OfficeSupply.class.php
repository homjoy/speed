<?php
namespace Atom\Package\Approval;

/**
 * 办公用品详情
 * @author haibinzhou@meilishuo.com
 * @since 2015-12-02
 */

use Atom\Package\Common\BaseQuery;

class OfficeSupply extends BaseQuery{

    private static $col = array('id','supply_name', 'detail', 'supply_type','order_type','memo', 'status');
    private static $pk = 'id';
    private static $fields = array(
        'id'             => 0,
        'supply_name'   => '',
        'detail'         => '',
        'supply_type'   => 0,
        'order_type'     => 0,
        'status'         => 1,
        'memo'           => '',
    );

    /**
     * 数据库名
     * @return string
     */
    public static function database(){
        return 'administration';
    }

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'office_supply';
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
        if (isset($params['id'])) {    //主键
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
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

        if (isset($params['supply_type'])) {   //按办公用品类型来查询
            if (is_array($params['supply_type'])) {
                $builder->whereIn('supply_type', $params['supply_type']);
            }else{
                $builder->where('supply_type', '=', $params['supply_type']);
            }
        }

        if (isset($params['order_type'])) {   //按审批流来查询
            if (is_array($params['order_type'])) {
                $builder->whereIn('order_type', $params['order_type']);
            }else{
                $builder->where('order_type', '=', $params['order_type']);
            }
        }

        if (isset($params['output_manager'])) {   //按发放人
            if (is_array($params['output_manager'])) {
                $builder->whereIn('output_manager', $params['output_manager']);
            }else{
                $builder->where('output_manager', '=', $params['output_manager']);
            }
        }
        //按办公用品名称查询
        if (!empty($params['supply_name'])) {
            if (isset($params['match']) ) {
                switch($params['match']){
                    case 'like':
                        $builder->where('supply_name', 'LIKE', '%'.$params['supply_name'].'%');
                        break;
                    case '=':
                        $builder->where('supply_name', '=', $params['supply_name']);
                        break;
                    default:
                        $builder->where('supply_name', '=', $params['supply_name']);
                        break;
                }
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

        $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('id','ASC');

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
            return FALSE;
        }

        $builder = $this->builder();

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[static::pk()];
        unset($params[static::pk()]);

        $builder->where(static::pk(), $pkid);
        return $builder->update($params);
    }

    /*
     * 添加申请
     *
     */
    public function insert($params) {
        $builder = $this->builder();

        if (!isset($params['supply_name']) || !isset($params['supply_type']) || !isset($params['order_type'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

}
