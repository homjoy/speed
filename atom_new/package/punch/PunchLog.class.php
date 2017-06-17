<?php
namespace Atom\Package\Punch;

use Atom\Package\Common\BaseQuery;

/**
 * 门禁打卡相关类
 * Class CreatePunchLog
 * @package Atom\Package\Punch
 * @author guojiezhu@meilishuo.com
 * @since 2015-10-19
 */
class PunchLog extends BaseQuery
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
        return 'punch_log';
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
            'user_id',
            'staff_id',
            'user_name',
            'card_number',
            'event_time'
        );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
            'id'          => 0,
            'user_id'     => 0,
            'staff_id'    => '',
            'user_name'   => '',
            'card_number' => '',
            'event_time'  => '',
        );

    public static function getFields()
    {
        return self::$fields;
    }

    public static $punch_staff_relation_fields = array('user_id','sftaff_id','status','name_cn','punch_staff_id','update_time');
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
            return '';
        }
        try {
            $builder = $this->builder();
            $data = array_intersect_key($data, self::$fields);
            $punchLogIds = $builder->insert($data);
            return $punchLogIds;
        } catch (\Exception $e) {
            return 0;
        }
    }
    /**
     * 获取员工的一天的 第一次和最后一次打卡记录
     * @param type $params
     */
    public function getStaffDailyPunchLog($params = array()){
        if (!is_array($params)) {
            return FALSE;
        }

        //查询
        $builder = $this->builder();
        //直接查询 最小的打卡和最大的打卡时间
        $select_field = array($builder->raw('min(punch_log.event_time) as start_time'),$builder->raw('max(punch_log
        .event_time) as end_time'),'punch_staff_relation.*');
        $builder->select($select_field);
        
        $builder->leftJoin('punch_staff_relation', 'punch_staff_relation.punch_staff_id', '=', 'punch_log.staff_id');
       
        if(isset($params['start_time'])|| isset($params['end_time'])){
            $builder->where(function($builder)use($params){
            if (isset($params['start_time'])  && isset($params['end_time'])){
                $builder->whereBetween('event_time',$params['start_time'],$params['end_time']);
            }  elseif (isset($params['end_time'])){
                $builder->where('event_time','<=', $params['start_time']);
            }  elseif(isset($params['start_time'])) {
                $builder->where('event_time','>=', $params['start_time']);
            }
            });
        }
        if(isset($params['name_cn']) ){
             $builder->where('name_cn','LIKE', '%'.$params['name_cn'].'%');
        }
        $builder->where('punch_staff_relation.user_id','!=', '');
        //$builder->where('punch_staff_relation.name_cn','like', '%刘红玉%');
        $builder ->groupBy('punch_log.staff_id');

//
        //$builder->hash(static::pk());
        //$queryObj = $builder->getQuery();
        //echo $queryObj->getRawSql();
        //exit;

        return $builder->get();
        
    }
}