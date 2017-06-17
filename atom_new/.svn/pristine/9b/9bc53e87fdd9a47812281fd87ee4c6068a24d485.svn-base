<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;
use Atom\Package\Helper\StaffDBHelper;
/**
 *
 * Class DepartmentRelation
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-01
 */

class DepartmentRelation extends BaseQuery {

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
        return 'department_relation';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'relation_id';
    }

    public static $col = array('relation_id', 'depart_id','parent_relation_id','role_id', 'user_id', 'update_time','memo','status','is_virtual');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'relation_id'         => 0,
        'depart_id'           => 0,
        'parent_relation_id'  => 0,
        'role_id'             => 0,
        'user_id'             => 0,
        'update_time'         => '0000-00-00',
        'memo'                => '',
        'status'              => 1,
        'is_virtual'          => 0,
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
        //部门是否虚拟：默认不是虚拟部门
        if (!isset($params['is_virtual'])) {
            $params['is_virtual'] = array(0);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            }else{

                $builder->where('depart_id', '=', $params['depart_id']);
            }
        }

        if (!empty($params['relation_id'])) {
            if (is_array($params['relation_id'])) {
                $builder->whereIn('relation_id', $params['relation_id']);
            }else{
                $builder->where('relation_id', '=', $params['relation_id']);
            }
        }
        if (!empty($params['parent_relation_id'])) {
            if (is_array($params['parent_relation_id'])) {
                $builder->whereIn('parent_relation_id', $params['parent_relation_id']);
            }else{
                $builder->where('parent_relation_id', '=', $params['parent_relation_id']);
            }
        }
        if (!empty($params['role_id'])) {
            if (is_array($params['role_id'])) {
                $builder->whereIn('role_id', $params['role_id']);
            }else{
                $builder->where('role_id', '=', $params['role_id']);
            }
        }
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
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

        //是否是虚拟部门
        if (!empty($params['is_virtual'])) {
            $builder->whereIn('is_virtual', $params['is_virtual']);
        }else{
            $builder->where('is_virtual', '=', $params['is_virtual']);
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

        return $builder->hash('relation_id')->offset($offset)->limit($limit)->get();
    }

     //更新
    public function  updateById($params){
        if(isset($params['relation_id'])  && $params['relation_id'] > 0) {
            $id = intval($params['relation_id']);
            unset($params['relation_id']);
            return $this->builder()
                ->where('relation_id',$id)->update($params);
        }
        if(isset($params['depart_id'])  && $params['depart_id'] > 0) {
            $id = intval($params['depart_id']);
            unset($params['depart_id']);
            return $this->builder()
                ->where('depart_id',$id)->update($params);
        }
         return FALSE;
    }
     public function insert($params) { //考虑到部门必须有人管 user_id 不可为空

        if (empty($params['depart_id']) || empty($params['role_id'])){
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
        $sql = 'insert into ' . self::tableName() . ' select * from department_relation_temp';

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
