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
class PunchDailyLog extends BaseQuery
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
        return 'punch_daily_log';
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
         'status',
         'name_cn',
         'start_time',
         'end_time',
         'attendance_date',
         'update_time',
         'approval_half',
         'approval_am_type',
         'approval_pm_type',
         'statistical_date',
         'is_statistics',
         'approval_am_status',
         'approval_pm_status',
         'late_time',
         'early_time',
         'abnormal_state',

        );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'id' => 0,
        'user_id' => 0,
        'staff_id' => '',
        'status' => 0,
        'name_cn' => '',
        'start_time' => '0000-00-00 00:00:00',
        'end_time' => '0000-00-00 00:00:00',
        'attendance_date' => '0000-00-00',
        'approval_half' => 1,
        'approval_am_type' => 0,
        'approval_pm_type' => 0,
        'statistical_date' => '0000-00-00 00:00:00',
        'is_statistics' => 0,
        'approval_am_status' => 0,
        'approval_pm_status' => 0,
        'late_time' => 0,
        'early_time' => 0,
        'abnormal_state' => 0
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
    public function savePuncDailyhLog($data)
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
     * 获取数据
     * @param array $params
     * @param int   $offset
     * @param int   $limit
     *
     * @return mixed
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {

        if (!is_array($params)) {
            return FALSE;
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
        //name_cn
        if (!empty($params['name_cn'])) {
            switch (isset($params['match']) ) {
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

        //staff_id
        if (!empty($params['staff_id'])) {
            $builder->where('staff_id', 'LIKE', '%'.$params['staff_id'].'%');
        }
        if(isset($params['start_time'])|| isset($params['end_time'])){
            $builder->where(function($builder)use($params){
                if (isset($params['start_time'])  && isset($params['end_time'])){
                    $builder->where('attendance_date','<=',$params['end_time']);
                    $builder->where('attendance_date','>=', $params['start_time']);
                }  elseif (isset($params['end_time'])){
                    $builder->where('attendance_date','<=', $params['end_time']);
                }  elseif(isset($params['start_time'])) {
                    $builder->where('attendance_date','>=', $params['start_time']);
                }
            });
        }
        if(isset($params['attendance_start_date'])|| isset($params['attendance_end_date'])){
            $builder->where(function($builder)use($params){
                if (isset($params['attendance_start_date'])  && isset($params['attendance_end_date'])){
                    $builder->where('attendance_date','<=',$params['attendance_end_date']);
                    $builder->where('attendance_date','>=', $params['attendance_start_date']);
                }  elseif (isset($params['attendance_end_date'])){
                    $builder->where('attendance_date','<=', $params['attendance_end_date']);
                }  elseif(isset($params['attendance_start_date'])) {
                    $builder->where('attendance_date','>=', $params['attendance_start_date']);
                }
            });
        }
        if (!empty($params['attendance_date'])) {
            $builder->where('attendance_date', '=', $params['attendance_date']);
        }

        $builder->orderBy('attendance_date','asc');

        if(isset($params['count']) && $params['count'] == 1){
            return $builder->count();
        }
        if(isset($params['all']) && $params['all'] == 1){
            return $builder->hash(static::pk())->get();
        }
//                        $builder->hash(static::pk());
//                $queryObj = $builder->getQuery();
//                echo $queryObj->getRawSql();
//        exit;
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /**
     * 通过主键，对考勤数据进行更新
     * @param $params
     *
     * @return bool
     */
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
    
    
}