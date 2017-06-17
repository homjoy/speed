<?php

namespace Atom\Package\Punch;

use Atom\Package\Common\BaseQuery;

/**
 * 用户和部门需要同步的数据的队列
 * Class CreatePunchLog
 * @package Atom\Package\Punch
 * @author guojiezhu@meilishuo.com
 * @since 2015-10-19
 */
class PunchSynUserDepart extends BaseQuery {

    /**
     * @return string
     */
    public static function database() {
        return 'worker';
    }

    /**
     * @return string
     */
    public static function tableName() {
        return 'punch_syn_user_depart';
    }

    /**
     * @return string
     */
    public static function pk() {
        return 'id';
    }

    public static $col = array(
        'id',
        'content',
        'channel',
        'send_at',
        'status',
    );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id' => 0,
        'content' => '',
        'channel' => 0,
        'send_at' => '',
        'status' => 0,
    );

    public static function getFields() {
        return self::$fields;
    }

    /**
     * 保存需要通同步的数据
     *
     * @param $data
     *
     * @return array
     * @throws \Exception
     */
    public function saveUserDepart($data) {
        if (empty($data)) {
            return '';
        }
        try {
            $builder = $this->builder();
            $data = array_intersect_key($data, self::$fields);
            $punchLogIds = $builder->insert($data);
            return $punchLogIds;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取队列中的数据
     * @param type $params
     * @param type $offset
     * @param type $limit
     * @return boolean
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100) {

        if (!is_array($params)) {
            return FALSE;
        }
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(0);
        }
        //查询
        $builder = $this->builder();
        $builder->select(static::$col);
        //状态
        if (is_array($params['status'])) {
            $builder->whereIn('status', $params['status']);
        } else {
            $builder->where('status', '=', $params['status']);
        }
        if (isset($params['channel'])) {
            $builder->where('channel', '=', $params['channel']);
        }
        $builder->orderBy(static::pk(), 'asc')->get();

        //是否获取全部
        if (isset($params['all']) && $params['all'] == 1) {
            return $builder->hash(static::pk())->get();
        }
        if (isset($params['count']) && $params['count'] == 1) {
            return $builder->count();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }
    
    /**
     * 更新
     */
    public function  updateByIds($params){
        $pk = static::pk();
        if(isset($params[$pk]) && $params[$pk] > 0){
            $id_array = $params[$pk];
            unset($params[$pk]);
           
            return $this->builder()
                ->whereIn($pk,$id_array)->update($params);
        }
         return FALSE;
    }

}
