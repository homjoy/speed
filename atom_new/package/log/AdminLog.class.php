<?php
namespace Atom\Package\Log;

use Atom\Package\Common\BaseQuery;
use Atom\Package\Helper\AdministrationDBHelper;
use Libs\Util\ArrayUtilities;

/**
 *
 * Class Logs
 * @package Admin\Package\logs;
 *
 */

class AdminLog extends  BaseQuery{
    /**
     * @return string
     */
    public static function database()
    {
        return 'administration';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'handle_logs';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'name', 'user_id', 'handle_type', 'handle_id', 'before_data', 'after_data', 'update_time',
                               'create_reason','operation_type' );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'             => 0,
        'name'           => '',
        'user_id'        => 0,
        'handle_type'    => 0,
        'handle_id'      => 0,
        'before_data'    => '',
        'after_data'     => '',
        //'update_time'  => '',
        'create_reason'   => '',
        'operation_type' => '',
    );


    public static function getFields()
    {
        return self::$fields;
    }



    /**
     * 获取信息
     * edit by guojiezhu 增加 获取全部页数的数据
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {

        if (!is_array($params)) {
            return FALSE;
        }
        $builder = $this->builder();
        $builder->select(static::$col);


        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }
        //after_data
        if (!empty($params['after_data'])) {
            $builder->where('after_data', 'LIKE', '%'.$params['after_data'].'%');
        }
        //id
        if (!empty($params['handle_id'])) {
            if (is_array($params['handle_id'])) {
                $builder->whereIn('handle_id', $params['handle_id']);
            }else{
                $builder->where('handle_id', '=', $params['handle_id']);
            }
        }
        //Type
        if (!empty($params['handle_type'])) {
            if (is_array($params['handle_type'])) {
                $builder->whereIn('handle_type', $params['handle_type']);
            }else{
                $builder->where('handle_type', '=', $params['handle_type']);
            }
        }

        // 操作类型 add update delete
        if (!empty($params['operation_type'])) {
                $builder->where('operation_type', '=', $params['operation_type']);

        }
        //update_time
        if(!empty($params['update_time'])|| !empty($params['end_time'])){
            $builder->where(function($builder)use($params){
                if (!empty($params['update_time'])  && !empty($params['end_time'])){
                    $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
                }  elseif (!empty($params['end_time'])){
                    $builder->where('update_time','<=', $params['end_time']);
                }  elseif(!empty($params['update_time'])) {
                    $builder->where('update_time','>=', $params['update_time']);
                }
            });
        }
        $builder->orderBy('id','desc');
//
//                $builder->hash(static::pk());
//                $queryObj = $builder->offset(($offset-1)*$limit)->limit($limit)->getQuery();
//                return $queryObj->getRawSql();



        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash('user_id')->get();
        }
        //获取总页数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        return $builder->hash('id')->offset(($offset-1)*$limit)->limit($limit)->get();
    }


    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk]) && $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        //根据user_id更新
        if(!empty($params['handle_type'])  && !empty($params['handle_id'])) {
            $type = intval($params['handle_type']);
            $id = intval($params['handle_id']);
            unset($params['handle_type']);
            unset($params['handle_ id']);
            $this->builder()->where('handle_type',$type);
            return  $this->builder()->where('handle_id',$id)->update($params);
        }
        return FALSE;

    }
    /**
     * 添加
     */
    public function insert($params=array()) {

        if (empty($params['user_id'])||empty($params['handle_id'])||empty($params['handle_type'])||empty($params['after_data'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);
        //return $params;
        return $this->builder()->insert($params);

    }

}