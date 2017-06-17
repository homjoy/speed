<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserInfo
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 *
 */

class UserInfo extends BaseQuery {

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
        return 'user_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'user_id';
    }

    public static $col = array('user_id', 'depart_id', 'job_role_id', 'name_cn', 'name_en', 'mail', 'hire_time', 'positive_time', 'graduation_time', 'staff_id', 'gender', 'update_time', 'status', 'flag','direct_leader');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'user_id'       => 0,
        'depart_id'     => 0,
        'job_role_id'   => 0,
        'name_cn'       => '',
        'name_en'       => '',
        'mail'          => '',
        'hire_time'         => '0000-00-00',
        'positive_time'     => '0000-00-00',
        'graduation_time'   => '0000-00-00',
        'staff_id'      => '',
        'gender'        => 0,
        'status'        => 1, //在职状态:1在职2已离职3重新入职
        'flag'          => 2, //标记:1实习2试用3正式4申请离职
        'direct_leader' =>0
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

        //在职状态：默认在职
        if (!isset($params['status'])) {
            $params['status'] = array(1,3);
        }
        //标记：默认所有状态
        if (!isset($params['flag'])) {
            $params['flag'] = array(1,2,3,4);
        }

        //查询
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

        //depart_id
        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('depart_id', $params['depart_id']);
            }else{
                $builder->where('depart_id', '=', $params['depart_id']);
            }
        }

        //job_role_id
        if (!empty($params['job_role_id'])) {
            if (is_array($params['job_role_id'])) {
                $builder->whereIn('job_role_id', $params['job_role_id']);
            }else{
                $builder->where('job_role_id', '=', $params['job_role_id']);
            }
        }

        //mail可进行模糊查询
        if (!empty($params['mail'])) {
            if(isset($params['match'])){
                switch ($params['match']){
                    case 'like':
                        $builder->where('mail', 'LIKE', '%'.$params['mail'].'%');
                        break;
                    case '=':
                        $builder->where('mail', '=', $params['mail']);
                        break;
                    default:
                        $builder->where('mail', '=', $params['mail']);
                        break;
                }
            }else{
                if (is_array($params['mail'])) {
                    $builder->whereIn('mail', $params['mail']);
                }else{
                    $builder->where('mail', '=', $params['mail']);
                }
            }
        }


        //name_cn
        if (!empty($params['name_cn'])) {
            if(isset($params['match'])){
                switch ($params['match']) {
                    case 'like':
                        $builder->where('name_cn', 'LIKE', '%'.$params['name_cn'].'%');
                        break;
                    case '=':
                        $builder->where('name_cn', '=', $params['name_cn']);
                        break;
                    default:
                        $builder->where('name_cn', '=', $params['name_cn']);
                        break;
                }
            }else{
                if (is_array($params['name_cn'])) {
                    $builder->whereIn('name_cn', $params['name_cn']);
                }else{
                    $builder->where('name_cn', '=', $params['name_cn']);
                }
            }
        }

        //hire_time
        if (!empty($params['hire_time'])) {
            if(isset($params['hire_time'])){
                switch ($params['match']) {
                    case 'like':
                        $builder->where('hire_time', 'LIKE', '%'.$params['hire_time']);
                        break;
                    case '=':
                        $builder->where('hire_time', '=', $params['hire_time']);
                        break;
                    default:
                        $builder->where('hire_time', '=', $params['hire_time']);
                        break;
                }
            }else{
                if (is_array($params['hire_time'])) {
                    $builder->whereIn('hire_time', $params['hire_time']);
                }else{
                    $builder->where('hire_time', '=', $params['hire_time']);
                }
            }
        }

        //name_en
        if (!empty($params['name_en'])) {
            if(isset($params['match'])){
                switch ($params['match']) {
                    case 'like':
                        $builder->where('name_en', 'LIKE', '%'.$params['name_en'].'%');
                        break;
                    case '=':
                        $builder->where('name_en', '=', $params['name_en']);
                        break;
                    default:
                        $builder->where('name_en', '=', $params['name_en']);
                        break;
                }
            }else{
                if (is_array($params['name_en'])) {
                    $builder->whereIn('name_en', $params['name_en']);
                }else{
                    $builder->where('name_en', '=', $params['name_en']);
                }
            }
        }
        //staff_id
        if (!empty($params['staff_id'])) {
            if(isset($params['match'])){
                switch ($params['match']) {
                    case 'like':
                        $builder->where('staff_id', 'LIKE', '%'.$params['staff_id'].'%');
                        break;
                    case '=':
                        $builder->where('staff_id', '=', $params['staff_id']);
                        break;
                    default:
                        $builder->where('staff_id', '=', $params['staff_id']);
                        break;
                }
            }else{
                if (is_array($params['staff_id'])) {
                    $builder->whereIn('staff_id', $params['staff_id']);
                }else{
                    $builder->where('staff_id', '=', $params['staff_id']);
                }
            }
        }
        if(isset($params['update_time'])|| isset($params['end_time'])){
            $builder->where(function($builder)use($params){
                if (isset($params['update_time'])  && isset($params['end_time'])){
                    $builder->whereBetween('update_time',$params['update_time'],$params['end_time']);
                }  elseif (isset($params['end_time'])){
                    $builder->where('update_time','<=', $params['end_time']);
                }  elseif(isset($params['update_time'])) {
                    $builder->where('update_time','>=', $params['update_time']);
                }
            });
        }
        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        }else{
            $builder->where('status', '=', $params['status']);
        }

        //var_dump($params['hire_start_time'],$params['hire_end_time']);//对入职时间的周宏编写
        if(isset($params['hire_start_time'])|| isset($params['hire_end_time'])){
            $builder->where(function($builder)use($params){
                if (!empty($params['hire_start_time'])  && !empty($params['hire_end_time'])){
                    $builder->whereBetween('hire_time',$params['hire_start_time'],$params['hire_end_time']);
                }  elseif (!empty($params['hire_end_time'])){
                    $builder->where('hire_time','<=', $params['hire_end_time']);
                }  elseif(!empty($params['hire_start_time'])) {
                    $builder->where('hire_time','>=', $params['hire_start_time']);
                }

            });
        }

        $builder->orderBy(static::pk(),'asc');

        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }


    public function  updateById($params){

        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }

        return FALSE;
    }
    //模糊查询
    public function getDataLike($params = array(),$like_params =  array(), $offset = 0, $limit = 100)  {

        $builder = $this->builder();
        $builder->select(static::$col);
        if (!is_array($params) && !is_array($like_params)) {//判断
            return FALSE;
        }
        //like 取合集
        $like_params = array_filter($like_params);
        $sum =count($like_params);
        $temp_sum = $sum;
        $builder->where(function($builder)use($like_params,$temp_sum ,$sum){
            foreach ($like_params as $key => $value) {
                $value = trim($value);
                if($sum==$temp_sum){
                    $builder->where($key, 'LIKE', '%'.$value.'%');
                }else{
                    $builder->orwhere($key, 'LIKE', '%'.$value.'%');
                }
                --$temp_sum;
            }
        });
        //hire
        if(isset($params['hire_start_time'])|| isset($params['hire_end_time'])){
            $builder->where(function($builder)use($params){
                if (!empty($params['hire_start_time'])  && !empty($params['hire_end_time'])){
                    $builder->whereBetween('hire_time',$params['hire_start_time'],$params['hire_end_time']);
                }  elseif (!empty($params['hire_end_time'])){
                    $builder->where('hire_time','<=', $params['hire_end_time']);
                }  elseif(!empty($params['hire_start_time'])) {
                    $builder->where('hire_time','>=', $params['hire_start_time']);
                }

            });
        }
        //常规字段
        foreach ($params as $key => $value) {
            if (!empty($params[$key])&& $key != 'all'&& $key != 'count') {
                if($key =='hire_end_time'||$key =='hire_start_time'){
                    continue;
                }
                if (is_array($params[$key])) {
                    $builder->whereIn($key, $value);
                }else{
                    $builder->where( $key, '=',$value);
                }
            }
        }
        // 全部查找
        $builder->orderBy(static::pk(),'asc');
        if(isset($params['all'])&& $params['all']== 1){
            return $builder->hash('user_id')->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash('user_id')->offset($offset)->limit($limit)->get();
    }
    /**
     * 添加
     */
    public function insert($params) {
        //名字和邮箱
        if (empty($params['name_cn']) || empty($params['mail'])||empty($params['depart_id'])||empty($params['staff_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}