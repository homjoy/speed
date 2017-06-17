<?php
namespace Atom\Package\Worker;

use Atom\Package\Common\BaseQuery;
use Libs\Util\ArrayUtilities;


/**
 * Notify原子层处理接口
 * @package Atom\Package\Worker
 * @author hongzhou@meilishuo.com
 * @since 2015-12-25
 */

class Notify extends BaseQuery {

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
        return 'notifications';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'notify_id';
    }

    public static $col = array('notify_id', 'pusher_id', 'custom_id', 'title', 'content', 'template_id', 'channel', 'from_id', 'to_id', 'mail', 'phone', 'send_at','status','update_time','weights','send_times','after_content');

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'notify_id'   => 0,
        'pusher_id'   => 0,
        'custom_id'   => 0,
        'title'       => '',
        'content'     => '',
        'template_id' => 0,
        'channel'     => 1,
        'update_time'   => '0000-00-00',
        'from_id'     => 0,
        'to_id'       => 0,
        'mail'        => '',
        'phone'       => '',
        'send_at'     => '',
        'status'      => 1,
        'weights'     => 100,
        'send_times'  =>0,
        'after_content' =>''
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

        //to_id
        if (!empty($params['to_id'])) {
            if (is_array($params['to_id'])) {
                $builder->whereIn('to_id', $params['to_id']);
            }else{
                $builder->where('to_id', '=', $params['to_id']);
            }
        }

        //channel
        if (!empty($params['channel'])) {
            if (is_array($params['channel'])) {
                $builder->whereIn('channel', $params['channel']);
            }else{
                $builder->where('channel', '=', $params['channel']);
            }
        }

        if (!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }

        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        return $builder->hash(static::pk())->orderBy('update_time','DESC')->offset($offset)->limit($limit)->get();
    }


}
