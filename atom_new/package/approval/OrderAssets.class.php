<?php
namespace Atom\Package\Approval;

use Atom\Package\Common\BaseQuery;

/**
 * 固定资产信息
 * @author minggeng@meilishuo.com
 * @since 2016-2-29
 */
class OrderAssets extends BaseQuery{

    private static $pk = 'order_id';

    private static $fields = array(
        'order_id'      => 0,
        'user_id'       => 0,
        'task_id'       => 0,
        'apply_type'    => 1,
        'status'        => 0,
        'output'        => 2,
        'output_manager' =>0,
        'reason'        => '',
        'reject_reason' => '',
        'update_time'   => '0000-00-00 00:00:00',
        'create_time'   => '0000-00-00 00:00:00',
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_assets';
    }

    public static function pk(){
        return self::$pk;
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * @return \Atom\Package\Approval\OrderAssets
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

        //apply_type
        if (!empty($params['apply_type'])) {
            if (is_array($params['apply_type'])) {
                $qb->whereIn('apply_type', $params['apply_type']);
            }else{
                $qb->where('apply_type', '=', $params['apply_type']);
            }
        }

        //status
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $qb->whereIn('status', $params['status']);
            }else{
                $qb->where('status', '=', $params['status']);
            }
        }

        //user_id
        if(!empty($params['user_id'])){
            if (is_array($params['user_id'])) {
                $qb->whereIn('user_id', $params['user_id']);
            }else{
                $qb->where('user_id', '=', $params['user_id']);
            }
        }

        //output
        if(!empty($params['output'])){
            if (is_array($params['output'])) {
                $qb->whereIn('output', $params['output']);
            }else{
                $qb->where('output', '=', $params['output']);
            }
        }


        if (isset($params['output_manager'])) {   //按发放人
            if (is_array($params['output_manager'])) {
                $qb->whereIn('output_manager', $params['output_manager']);
            }else{
                $qb->where('output_manager', '=', $params['output_manager']);
            }
        }

        //根据创建时间排序
        if(isset($params['create_time'])){
            $qb->where('create_time','>=',$params['create_time']);
        }
        if(isset($params['end_time'])){
            $qb->where('create_time','<=',$params['end_time']);
        }

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $qb->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $qb->hash(static::pk())->get();
        }


        //查询
        if(isset($params['sort'])){  //排序时用
            $qb->offset($offset)->limit($limit)->orderBy('create_time',$params['sort']);
        }else{
            $qb->offset($offset)->limit($limit)->orderBy('status','ASC')->orderBy('create_time','DESC');
        }

        /* $builder->hash(static::pk());
         $queryObj = $builder->getQuery();
         echo $queryObj->getRawSql();die; */
        $ret = $qb->hash(self::$pk)->get();
        return $ret;
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

        if (!isset($params['user_id']) || !isset($params['task_id'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }
}
