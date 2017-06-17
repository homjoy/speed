<?php
namespace Atom\Package\Itserver;
use Atom\Package\Common\BaseQuery;
/**
 * speed官方账号
 * @author hongzhou
 * @date 2015-01-19
 */
class OfficialMail extends BaseQuery{


    private static $col = array('mail_id', 'mail_name', 'status', 'update_time');
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
        return 'official_mail';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'mail_id';
    }
    /**
     * 所有字段及默认值
     */
    private static $fields = array(
        'mail_id'        => 0 ,
        'mail_name'   => '' ,
        'update_time'    => '0000-00-00' ,
        'status'         => 0,
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
    public function getDataList($params = array(), $offset = 0, $limit = 10)
    {
        if ( empty($params['status'])) {
            $params['status'] =array(0,1);
        }

        if (!is_array($params)) {
            return FALSE;
        }
        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //mail可进行模糊查询
        if (!empty($params['mail_name'])) {
            if(isset($params['match'])){
                switch ($params['match']){
                    case 'like':
                        $builder->where('mail_name', 'LIKE', '%'.$params['mail_name'].'%');
                        break;
                    case '=':
                        $builder->where('mail_name', '=', $params['mail_name']);
                        break;
                    default:
                        $builder->where('mail_name', '=', $params['mail_name']);
                        break;
                }
            }else{
                if (is_array($params['mail_name'])) {
                    $builder->whereIn('mail_name', $params['mail_name']);
                }else{
                    $builder->where('mail_name', '=', $params['mail_name']);
                }
            }
        }


        if (!empty($params['mail_id'])) {
            if (is_array($params['mail_id'])) {
                $builder->whereIn('mail_id', $params['mail_id']);
            }else{
                $builder->where('mail_id', '=', $params['mail_id']);
            }
        }
        if (!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }
        $builder->orderBy('mail_id','desc');

        if(isset($params['count'])&& $params['count']== 1){
            return $builder->hash('mail_id')->count();
        }
        return $builder->hash('mail_id')->offset($offset)->limit($limit)->get();
    }
    /**
     * 更新
     */
    public function  updateById($params){

        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        if(isset($params['mail_name'])  && !empty($params['mail_name'])) {

            return $this->builder()
                ->where('mail_name',$params['mail_name'])->update($params);
        }
        return FALSE;
    }
    /**
     * 添加
     */
    public function insert($params) {

        if (empty($params['mail_name'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);

        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);

    }


}
