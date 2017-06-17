<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;
use Atom\Package\Helper\StaffDBHelper;
/**
 * 数据备份
 * Class DepartmentSub
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-24
 */

class DepartmentSubTemp extends BaseQuery {

    /**
     * @return string
     */
    public static function database()
    {
        return 'staff';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'department_sub_temp';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'sub_id';
    }

    public static $col = array('sub_id', 'relation_id','user_id', 'update_time','memo','status');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'sub_id'              => 0,
        'relation_id'         => 0,
        'user_id'             => 0,
        //'update_time'         => '0000-00-00',
        'memo'                => '',
        'status'              => 1,
    );

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 获取信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {

        if (!is_array($params)) {
            return FALSE;
        }
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(1);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        if (!empty($params['sub_id'])) {
            if (is_array($params['sub_id'])) {
                $builder->whereIn('sub_id', $params['sub_id']);
            }else{
                $builder->where('sub_id', '=', $params['sub_id']);
            }
        }

        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }

        if (!empty($params['relation_id'])) {
            if (is_array($params['relation_id'])) {
                $builder->whereIn('relation_id', $params['relation_id']);
            }else{
                $builder->where('relation_id', '=', $params['relation_id']);
            }
        }

        //时间判定
        if (!empty($params['update_time'])  && !empty($params['end_time'])){
            $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
        }  elseif (!empty($params['end_time'])){
            $builder->where('update_time','<=', $params['end_time']);
        }  elseif(!empty($params['update_time'])){
            $builder->where('update_time','>=', $params['update_time']);
        }
        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        $builder->orderBy(static::pk(),'asc');

        //获取符合条件的总条数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->hash(static::pk())->count();
        }
        //获取符合条件的所有数据
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }

        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

     //更新
    public function  updateById($params){

        if(isset($params['sub_id'])  && $params['sub_id'] > 0) {
            $id = intval($params['sub_id']);
            unset($params['sub_id']);
            return $this->builder()->where('sub_id',$id)->update($params);
        }
        if(isset($params['relation_id'])  && $params['relation_id'] > 0) {
            $id = intval($params['relation_id']);
            unset($params['relation_id']);
            return $this->builder()->where('relation_id',$id)->update($params);
        }
         return FALSE;
    }
     public function insert($params) {

        if (empty($params['relation_id']) || empty($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

    /**
     * 添加
     */
    public function insertAll() {
        $sql = 'insert into ' . self::tableName() . ' select * from department_sub';

        return StaffDBHelper::getConn()->write($sql, array());
    }

    /**
     * 删除全部（谨慎使用）
     */
    public function deleteAll(){
        $sql = 'delete from ' . self::tableName();

        return StaffDBHelper::getConn()->write($sql, array());
    }
}
