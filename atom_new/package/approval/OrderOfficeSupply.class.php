<?php
namespace Atom\Package\Approval;

/**
 * 办公用品申请
 * @author haibinzhou@meilishuo.com
 * @since 2015-12-02
 */

use Atom\Package\Common\BaseQuery;

class OrderOfficeSupply extends BaseQuery{

    private static $col = array('order_id','user_id', 'task_id', 'parent_id','order_type','status','post_place', 'output', 'output_manager', 'reject_reason', 'update_time','create_time');
    private static $pk = 'order_id';
    private static $fields = array(
        'order_id'           => 0,
        'user_id'            => 0,
        'task_id'            => 0,
        'parent_id'          => 0,
        'order_type'         => 0,
        'status'             => 1,   //1新建2待接收3处理中4完成5驳回6失效
        'post_place'         => '',
        'create_time'        => '0000-00-00 00:00:00',
        'output'             => 2,  //默认没有发放
        'output_manager'     => 0,
        'reject_reason'      => '',
    );


    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_office_supply';
    }

    public static function pk(){
        return 'order_id';
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
        if (isset($params['order_id'])) {    //主键
            if (is_array($params['order_id'])) {
                $builder->whereIn('order_id', $params['order_id']);
            }else{
                $builder->where('order_id', '=', $params['order_id']);
            }
        }

        if (isset($params['task_id'])) {    //流程
            if (is_array($params['task_id'])) {
                $builder->whereIn('task_id', $params['task_id']);
            }else{
                $builder->where('task_id', '=', $params['task_id']);
            }
        }
        if (isset($params['status'])) {      //状态
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }

        if (isset($params['user_id'])) {   //按创建人查询
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        if (isset($params['parent_id'])) {   //获取同一个单子的数据
            if (is_array($params['parent_id'])) {
                $builder->whereIn('parent_id', $params['parent_id']);
            }else{
                $builder->where('parent_id', '=', $params['parent_id']);
            }
        }

        if (isset($params['order_type'])) {   //按审批流类型查询
            if (is_array($params['order_type'])) {
                $builder->whereIn('order_type', $params['order_type']);
            }else{
                $builder->where('order_type', '=', $params['order_type']);
            }
        }


        if (isset($params['output'])) {   //是否发放
            if (is_array($params['output'])) {
                $builder->whereIn('output', $params['output']);
            }else{
                $builder->where('output', '=', $params['output']);
            }
        }

        if (isset($params['post_place'])) {   //是那层发放的
            if (is_array($params['post_place'])) {
                $builder->whereIn('post_place', $params['post_place']);
            }else{
                $builder->where('post_place', '=', $params['post_place']);
            }
        }

        if (isset($params['output_manager'])) {   //按发放人
            if (is_array($params['output_manager'])) {
                $builder->whereIn('output_manager', $params['output_manager']);
            }else{
                $builder->where('output_manager', '=', $params['output_manager']);
            }
        }

        //根据创建时间查询
        if(isset($params['create_time'])){
            $builder->where('create_time','>=',$params['create_time']);
        }
        if(isset($params['end_time'])){
            $builder->where('create_time','<=',$params['end_time']);
        }

        //根据发放日期查询
        if(isset($params['output_start_time'])){
            $builder->where('update_time','>=',$params['output_start_time']);
        }
        if(isset($params['output_end_time'])){
            $builder->where('update_time','<=',$params['output_end_time']);
        }

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }


        //查询
        if(isset($params['sort'])){  //请假排序时用
            $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('create_time',$params['sort']);
        }else{
            $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('status','ASC')->orderBy('create_time','DESC');
        }

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

        if (!isset($params['user_id']) || !isset($params['task_id'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

}
