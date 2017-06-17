<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 * 外包用户管理表
 * Class UserOutsourcingInfo
 * @package Atom\Package\Account
 * @author haibinzhou@meilishuo.com
 *
 */

class UserOutsourcingInfo extends BaseQuery {

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
        return 'user_outsourcing_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'out_user_id';
    }

    public static $col = array('out_user_id', 'depart_id', 'job_role_id', 'name_cn', 'name_en', 'mail', 'hire_time', 'positive_time', 'graduation_time', 'staff_id', 'gender', 'update_time', 'status', 'flag','direct_leader','type','mail_suffix');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'out_user_id'       => 0,
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
        'direct_leader' => 0,
        'type'          => 0,
        'mail_suffix'   => ''
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
        if (!empty($params['out_user_id'])) {
            if (is_array($params['out_user_id'])) {
                $builder->whereIn('out_user_id', $params['out_user_id']);
            }else{
                $builder->where('out_user_id', '=', $params['out_user_id']);
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
            if (isset($params['match'])) {
                switch($params['match']){
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
            }
        }

        //name_en
        if (!empty($params['name_en'])) {
            if (isset($params['match']) ) {
                switch($params['match']){
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
            }
        }
        //staff_id
        if (!empty($params['staff_id'])) {
            if (isset($params['match']) ) {
                switch($params['match']){
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


        //类型
        if(!empty($params['type'])) {
            if (is_array($params['type'])) {
                $builder->whereIn('type', $params['type']);
            } else {
                $builder->where('type', '=', $params['type']);
            }
        }

        //邮箱后缀
        if (!empty($params['mail_suffix'])) {
            $builder->where('mail_suffix', '=', $params['mail_suffix']);
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

//
//
//                $builder->hash(static::pk());
//                $queryObj = $builder->getQuery();
//                return $queryObj->getRawSql();
//       exit;

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
            return $builder->hash('out_user_id')->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash('out_user_id')->offset($offset)->limit($limit)->get();
    }  
    /**
     * 添加
     */
    public function insert($params) {
       //名字和邮箱
        if (empty($params['name_cn']) || empty($params['mail'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);

        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}
