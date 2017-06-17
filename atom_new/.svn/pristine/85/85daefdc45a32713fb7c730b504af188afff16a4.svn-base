<?php
namespace Atom\Package\Approval;

/**
 * 附件信息
 * @author haibinzhou@meilishuo.com
 * @since 2015-11-19
 */

use Atom\Package\Common\BaseQuery;

class OrderAttachment extends BaseQuery{

    private static $col = array('id', 'order_id', 'order_type', 'update_time', 'local_file','service_file','status');
    private static $pk = 'id';
    private static $fields = array(
        'id'                 => 0,
        'order_id'           => 0,
        'order_type'         => 0,   //1有效，0无效
        'local_file'         => '',
        'service_file'       => '',
        'status'             => 1,
    );


    /**
     * 数据库表名
     * @return string
     */
    public static function tableName(){
        return 'order_attachment';
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

        if(!isset($params['status'])){
            $params['status'] = 1;
        }

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
        }

        if (isset($params['order_type'])) {
            if (is_array($params['order_type'])) {
                $builder->whereIn('order_type', $params['order_type']);
            }else{
                $builder->where('order_type', '=', $params['order_type']);
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
            return FALSE;
        }

        $builder = $this->builder();

        $params = array_intersect_key($params, self::$fields);
        $pkid = $params[static::pk()];
        unset($params[static::pk()]);

        $builder->where(static::pk(), $pkid);
        return $builder->update($params);
    }

    public function insert($params) {
        $builder = $this->builder();

        if (!isset($params['order_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

}
