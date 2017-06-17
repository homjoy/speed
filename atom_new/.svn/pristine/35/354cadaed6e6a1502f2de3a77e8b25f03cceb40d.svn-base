<?php
namespace Atom\Package\Approval;

/**
 * 请假信息
 * @author haibinzhou@meilishuo.com
 * @since 2015-08-11
 */

use Atom\Package\Common\BaseQuery;

class OrderLeave extends BaseQuery{

    private static $col = array('order_id','user_id', 'task_id', 'absence_type', 'start_date', 'start_half', 'end_date', 'end_half', 'length', 'last_year_used', 'this_year_used','memo', 'others', 'status', 'update_time', 'create_time','local_leave_file','service_leave_file');
    private static $pk = 'order_id';
    private static $fields = array(
        'order_id'           => 0,
        'user_id'            => 0,
        'absence_type'       => 1,  // 1事假2年假3病假4带薪病假5婚假6丧假7产假8陪产假9产检假10流产假
        'task_id'            => 0,
        'start_date'         => '0000-00-00',
        'start_half'         => '',
        'end_date'           => '0000-00-00',
        'end_half'           => '',
        'length'             => 0,
        'last_year_used'     => 0,
        'this_year_used'     => 0,
        'memo'               => '',
        'others'             => '',
        'status'             => 0,   //1新建2待接收3处理中4完成5驳回0失效
        'update_time'        => '0000-00-00 00:00:00',
        'create_time'        => '0000-00-00 00:00:00',
        'local_leave_file'   => '',
        'service_leave_file' => '',
    );


    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_leave';
    }

    public static function pk(){
        return 'order_id';
    }

    public static function getFields(){
        return self::$fields;
    }

    /**
     * 获取单条信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataById($order_id, $fields = array())
    {
        if (empty($order_id)) {
            throw new \InvalidArgumentException(__METHOD__." wrong parameter id.");
        }

        return $this->builder()->where(static::pk(),$order_id)->first();
    }

    //查询附件，同步使用
    public function getAttachment()
    {
        $builder = $this->builder();
        $builder->where('local_leave_file','!=','');
        $builder->where('service_leave_file','!=','');

        return $builder->get();

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
        if (isset($params['absence_type'])) {    //请假类型
            if (is_array($params['absence_type'])) {
                $builder->whereIn('absence_type', $params['absence_type']);
            }else{
                $builder->where('absence_type', '=', $params['absence_type']);
            }
        }
        if (isset($params['user_id'])) {   //按创建人查询
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        if (isset($params['start_date'])){
            $builder->where('start_date','>=',$params['start_date']);
        }

        if(isset($params['end_date'])){
            $builder->where('end_date','<=',$params['end_date']);
        }

        if (isset($params['gt_start_date'])){
            $builder->where('end_date','>=',$params['gt_start_date']);
        }

        if(isset($params['lt_end_date'])){
            $builder->where('start_date','<=',$params['lt_end_date']);
        }
        if(isset($params['after_end_date'])){
            $builder->where('end_date','>=',$params['after_end_date']);
        }

        if (isset($params['before_start_date'])){
            $builder->where('start_date','<=',$params['before_start_date']);
        }
        //根据创建时间排序
        if(isset($params['create_time'])){
            $builder->where('create_time','>=',$params['create_time']);
        }
        if(isset($params['end_time'])){
        $builder->where('create_time','<=',$params['end_time']);
        }

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }



        if(isset($params['leave'])){     //请假排序时用
            if(isset($params['sort'])){
                $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('create_time',$params['sort']);
            }else{
                $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('status','ASC')->orderBy('create_time','DESC');
            }
        }else{     //其他排序用
            if(isset($params['sort'])){
                $builder->select(static::$col)->offset($offset)->limit($limit)->orderBy('order_id',$params['sort']);
            }
        }

//        $builder->hash(static::pk());
//        $queryObj = $builder->getQuery();
//        echo $queryObj->getRawSql();die; 
        $builder->hash(self::$pk);

        return $builder->get();
    }


    /**
     * 添加
     */
    public function add($params) {
        $builder = $this->builder();
        $pk = static::pk();
        if (!isset($params['user_id']) || !isset($params['absence_type']) || !isset($params['start_date']) || !isset($params['end_date'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[$pk]);
        return $builder->onDuplicateKeyUpdate($params)->insert($params);
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
     * 同步请假脚本用
     *
     */
    public function insert($params) {
        $builder = $this->builder();

        if (!isset($params['user_id']) || !isset($params['absence_type']) || !isset($params['start_date']) || !isset($params['end_date'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

}
