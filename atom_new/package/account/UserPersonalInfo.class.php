<?php
namespace Atom\Package\Account;

use Atom\Package\Common\BaseQuery;

/**
 *
 * Class UserPersonalInfo
 * @package Atom\Package\Account
 *
 */

class UserPersonalInfo extends BaseQuery {

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
        return 'user_personal_info';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array('id', 'user_id', 'nation', 'birthday', 'mobile', 'mobile_another', 'telephone', 'qq', 'coat_size', 'coat_color', 'pants_size', 'shoes_size', 'others', );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id'            => 0,
        'user_id'       => 0,
        'nation'        => '',
        'birthday'      => '0000-00-00',
        'mobile'        => '',
        'mobile_another'    => '',
        'telephone'     => '',
        'qq'            => '',
        'coat_size'     => '',
        'coat_color'    => '',
        'pants_size'    => '',
        'shoes_size'    => '',
        'others'      => '',
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
        //匹配方式：默认严格匹配
        if (isset($params['match'])) {
            switch ($params['match']) {
                case 'like':
                    $params['match'] = 'LIKE';
                    break;
                case 'equal':
                    $params['match'] = '=';
                    break;
                default:
                    $params['match'] = '=';
                    break;
            }
        }else{
            $params['match'] = '=';
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

        //id
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }

        //birthday
        if (!empty($params['birthday'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('birthday', 'LIKE', '%'.$params['birthday']);
                    break;
                case '=':
                    $builder->where('birthday', '=', $params['birthday']);
                    break;
                default:
                    $builder->where('birthday', '=', $params['birthday']);
                    break;
            }
        }

        //mobile
        if (!empty($params['mobile'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('mobile', 'LIKE', '%'.$params['mobile'].'%');
                    break;
                case '=':
                    $builder->where('mobile', '=', $params['mobile']);
                    break;
                default:
                    $builder->where('mobile', '=', $params['mobile']);
                    break;
            }
        }

        //qq
        if (!empty($params['qq'])) {
            switch ($params['match']) {
                case 'LIKE':
                    $builder->where('qq', 'LIKE', '%'.$params['qq'].'%');
                    break;
                case '=':
                    $builder->where('qq', '=', $params['qq']);
                    break;
                default:
                    $builder->where('qq', '=', $params['qq']);
                    break;
            }
        }
        //update_time
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
        $builder->orderBy(static::pk(),'asc');
/*
        $builder->hash(static::pk());
        $queryObj = $builder->getQuery();
        echo $queryObj->getRawSql();
*/

        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash('user_id')->get();
        }
        //获取总页数
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        return $builder->hash('user_id')->offset($offset)->limit($limit)->get();
    }

    public function getDataLike($params =array(),$like_params =  array(), $offset = 0, $limit = 100)  {


        $builder = $this->builder();
        $builder->select(static::$col);
        if ( !is_array($like_params)) {//判断
            return FALSE;
        }
       //like 字段
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
       //限定字段
        foreach ($params as $key => $value) {
            if (!empty($params[$key]) && $key != 'all'&&$key != 'count') {
                if (is_array($params[$key])) {
                    $builder->whereIn($key, $value);
                }else{
                    $builder->where( $key, '=',$value);
                }
            }
        }

        $builder->orderBy(static::pk(),'asc');
        //全部查找
        if(isset($params['all'])&& $params['all']== 1){
            return $builder->hash('user_id')->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash('user_id')->offset($offset)->limit($limit)->get();
    }

    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        //根据user_id更新
        if(isset($params['user_id'])  && $params['user_id'] > 0) {
            $id = intval($params['user_id']);
            unset($params['user_id']);
            return $this->builder()
                ->where('user_id',$id)->update($params);
        }
         return FALSE;
    }
    /**
     * 添加
     */
    public function insert($params) {

        if (!isset($params['user_id'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }

}