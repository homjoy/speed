<?php
namespace Atom\Package\Itserver;

use Atom\Package\Common\BaseQuery;


/**
 * 用户注册信息
 * @author hongzhou
 * @date 2015-09-07
 */
class ItAccount extends BaseQuery{

    /**
     * 帐号类型
     */
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL = 5;
    public static $TYPE_WIFI = 6;
    public static $col = array('id', 'login_name','type', 'status','expire_time');

    /**
     * 所有字段及默认值
     */
    private static $fields = array(
        'id'   => '' ,
        'type' => 0 ,
        'login_name'   => '' ,
        'status'       => 1 ,
        'expire_time'       => '0000-00-00 00:00:00',
    );

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * @return string
     */
    public static function database()
    {
        return 'administration';
    }


    public static function tableName(){
        return 'it_account';
    }

    public static function pk(){
        return 'id';
    }

    /**
     * 批量获取用户的注册情况
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100){

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

        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }


        if (!empty($params['login_name'])) {
            if(isset($params['match'])){
                switch ($params['match']){
                    case 'like':
                        $builder->where('login_name', 'LIKE', '%'.$params['login_name'].'%');
                        break;
                    case '=':
                        $builder->where('login_name', '=', $params['login_name']);
                        break;
                    default:
                        $builder->where('login_name', '=', $params['login_name']);
                        break;
                }
            }else{
                if (is_array($params['login_name'])) {
                    $builder->whereIn('login_name', $params['login_name']);
                }else{
                    $builder->where('login_name', '=', $params['login_name']);
                }
            }
        }

        //status  hongzhou@meilishuo.com  添加
        if (!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }

        if(!empty($params['type'])){
            if (is_array($params['type'])) {
                $builder->whereIn('type', $params['type']);
            }else{
                $builder->where('type', '=', $params['type']);
            }
        }


        $builder->orderBy(static::pk(),'desc');

        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }

        return $builder->hash('id')->offset($offset)->limit($limit)->get();
    }

    /**
     * 更新
     * @param array 需要用主键或者用户id
     */
    public function updateDataById($params = array()) {

        if (!isset($params['id'])){
            return FALSE;
        }

        if(isset($params['id'])  && $params['id'] > 0) {
            $id = intval($params['id']);
            unset($params['id']);
            return $this->builder()
                ->where('id',$id)->update($params);
        }

        if(isset($params['type'])  && isset($params['login_name'])) {
            return $this->builder()
                ->where('type',$params['type'])
                ->where('login_name',$params['login_name'])->update($params);
        }

        return FALSE;
    }

    public function insert($params) {
        if (empty($params['type']) || empty($params['login_name'])){
            return FALSE;
        }

        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);
    }


}