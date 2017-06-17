<?php
namespace Worker\Package\Notification;
use Worker\Package\Common\BaseQuery;

/**
 *
 * Class Pusher
 * @package Worker\Package\Notification
 *
 */
class Pusher extends BaseQuery{

    /**
     * @param $params
     * @return array|string
     */
    public function create($params)
    {
        if(empty($params['app']) || empty($params['module'])){
            return 0;
        }
        $data = array_intersect_key($params, static::$fields);
        $data = array_merge(static::$fields, $data);
        unset($data['pusher_id']);
        //生成一个TOKEN
        $data['token'] = md5($params['app']. $params['module'] . uniqid(). time());

        return $this->builder()->insert($data);
    }

    /**
     * @param $params
     * @return $this|int
     */
    public function update($params)
    {
        if(!isset($params[static::pk()]) || empty($params[static::pk()])){
            return 0;
        }
        $pk = $params[static::pk()];
        $data = array_intersect_key($params, static::$fields);
        unset($params[static::pk()]);
        unset($params['token']); //避免修改token?

        return $this->builder()->where(static::pk(),$pk)->update($data);
    }

    /**
     * @param $app
     * @param $module
     * @return array|null|\stdClass
     */
    public function getByModule($app,$module)
    {
        if(empty($app) || empty($module)){
            return array();
        }

        $builder = $this->builder()->where('status',1);
        return $builder->where('app',$app)->where('module',$module)->first();
    }

    /**
     * @param $token
     * @return null|\stdClass
     */
    public function getByToken($token)
    {
        if(empty($token)){
            return array();
        }
        $builder = $this->builder()->where('status',1);
        return $builder->where('token',$token)->first();
    }




    /**
     * @return string
     */
    public static function database()
    {
        return 'worker';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'pusher';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'pusher_id';
    }

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'pusher_id'       => 0,
        'app'       => '',
        'module'       => '',
        'name'       => '',
        'token'       => '',
        'default_template'       => 0,
        'status'        => 1,
    );
}