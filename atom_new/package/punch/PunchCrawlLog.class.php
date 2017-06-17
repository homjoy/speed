<?php
namespace Atom\Package\Punch;

use Atom\Package\Common\BaseQuery;

/**
 * 门禁打卡抓取类
 * Class CreatePunchLog
 * @package Atom\Package\Punch
 * @author guojiezhu@meilishuo.com
 * @since 2015-10-19
 */
class PunchCrawlLog extends BaseQuery
{
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
    public static function tableName() {
        return 'punch_crawl_log';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'id';
    }

    public static $col = array(
            'id',
            'content',
            'status',
            'create_time',
            'identify'
        );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
            'id'          => 0,
            'content'     => '',
            'status'      => '',
            'create_time' => '',
            'identify'    => ''
    );

    public static function getFields()
    {
        return self::$fields;
    }


    /**
     * 保存打卡记录
     *
     * @param $data
     *
     * @return array
     * @throws \Exception
     */
    public function savePunchLog($data)
    {
        if (empty($data)) {
            return 0;
        }
        try {
            $builder = $this->builder();
            $data = array_intersect_key($data, self::$fields);
            $punchLogIds = $builder->insert($data);
            return $punchLogIds;
        } catch (\Exception $e) {
            //throw $e;
            return 0;
        }
    }


    /**
     * 获取信息
     * @param $id
     * @param array $fields $order  = array('method'=>'asc','field'='id')
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100,$order = array())
    {

        if (!is_array($params)) {
            return FALSE;
        }
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(0,9);
        }

        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //状态
        if(!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            } else {
                $builder->where('status', '=', $params['status']);
            }
        }

        //唯一标示符号
        if(!empty($params['identify'])) {
             $builder->where('identify', '=', $params['identify']);
        }

        if(empty($order)) {
            $builder->orderBy(static::pk(), 'asc')->get();
        }else{
            $builder->orderBy($order['field'],$order['method'])->get();
        }
        //是否获取全部
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }
        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }

        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /*
    * 更新
    */
    public function  updateByIds($params){
        $pk = static::pk();
        if(isset($params[$pk]) && is_array($params[$pk])){
            $id_array = $params[$pk];
            unset($params[$pk]);

            return $this->builder()
                ->whereIn($pk,$id_array)->update($params);
        }
        return FALSE;
    }

    /*
    * 通过参数更新
    */
    public function  updateByParams($condition,$params = array()){
        if (!is_array($condition) || !is_array($params)) {
            return FALSE;
        }
        $builder = $this->builder();
        //状态：默认有效
        if(!empty($condition['status'])) {
            if (is_array($condition['status'])) {
                $builder->whereIn('status', $condition['status']);
            } else {
                $builder->where('status', '=', $condition['status']);
            }
        }
        return $builder->update($params);

    }




}