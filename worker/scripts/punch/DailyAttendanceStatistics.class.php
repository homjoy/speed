<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');

/**
 *  每日考勤数据统计
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-11-10
 */
class DailyAttendanceStatistics extends \Frame\Script
{

    protected $params = array();
    protected $errors = array();
    protected $page_size = 50;
    protected $return_statis = array();
    public function run()
    {
        //$args = $this->request->arg ['arg'];
        $start = microtime(true);
        //考勤时间
        $current_date = date('Y-m-d', strtotime('-1 day'));
        //获取所有的用户和考勤规则
        $attendance_staff = $this->getAttendanceStaff();

        //获取节假日和工作日
        $working_params = array('start_date' => $current_date, 'end_date' => $current_date);
        $working_calendar_list = $this->getWorkingCalendarList($working_params);
        if (!empty($attendance_staff)) {
            //循环需要考勤的人
            //用户的考勤记录
            foreach ($attendance_staff as $staff) {
                $punch_log_prams = array(
                    'user_id'    => $staff['user_id'],
                    'start_time' => $current_date,
                    'end_time'   => $current_date,
                    'all'        => 1,
                );
                //获取指定人的打卡记录
                $all_punch_log = $this->getDailyPunchLog($punch_log_prams);

                //如果当前没有，则保存进入数据库
                $default_daily_punch_info = array(
                    'user_id'    => $staff['user_id'],
                    'staff_id'   => $staff['staff_id'],
                    'name_cn'    => $staff['name_cn'],
                    'status'     => $staff['status'],
                    'end_time'   => '0000-00-00 00:00:00',
                    'start_time' => '0000-00-00 00:00:00',
                );
                //统计考勤的时间，这个人的打卡信息
                if (!empty($all_punch_log)) {
                    //当前的打卡的数据
                    $current_date_punch_log = current($all_punch_log);
                    //最后一次打卡时间
                    $current_date_punch_log['end_time'] == '0000-00-00 00:00:00' ? $last_punch_time = 0
                        : $last_punch_time = strtotime($current_date_punch_log['end_time']);
                    //晚上考勤时间标准
                    $afternoon_end = strtotime($current_date . ' ' . trim($staff['afternoon_end']));
                    //第一次打卡时间
                    $current_date_punch_log['start_time'] == '0000-00-00 00:00:00' ? $first_punch_time = 0
                        : $first_punch_time = strtotime($current_date_punch_log['start_time']);
                    //早上考勤时间标准
                    $morning_start = strtotime($current_date . ' ' . trim($staff['morning_start']));
                } else {
                    $current_date_punch_log = array();
                    $last_punch_time = 0;
                    $first_punch_time = 0;
                    $afternoon_end = 0;
                    $morning_start = 0;

                }

                //当天的假期信息
                $current_calendar_list = !empty($working_calendar_list[$current_date]) ? $working_calendar_list[$current_date] : array();
                $attendance_params = array(
                    'calendar_list'      => $current_calendar_list,
                    'punch_log'          => $current_date_punch_log,
                    'default_punch_info' => $default_daily_punch_info,
                    'current_date'       => $current_date,
                    'last_punch_time'    => $last_punch_time, //最后一次打卡时间
                    'first_punch_time'   => $first_punch_time,//第一次打卡时间
                    'afternoon_end'      => $afternoon_end,//下午下班时间
                    'morning_start'      => $morning_start,//早上上班时间
                );


                //1.判断休息日
                $week = date('w', strtotime($current_date));
                //是 周六日 且 没有在 串休表中 即 type = 2
                if ($week == 0 || $week == 6) {
                    // ($calendar_list,$punch_log,$default_punch_info,$current_date)
                    //如果 这一天是周六日，并且 在假期表中，不是串休的，直接返回
                    $stat_return = $this->saturdayJudgment($attendance_params);
                    if ($stat_return) {
                        continue;
                    }
                }

                //2. 是法定节假日
                $holiday_return = $this->holidayJudgment($attendance_params);
                if ($holiday_return) {
                    continue;
                }

                //3.判断是否请假 ,请假做了上午和下午的处理
                if (!empty($current_staff_leave)) {
                    $leave_return = $this->leaveJudgement($attendance_params);
                    if ($leave_return) {
                        continue;
                    }
                }

                //4.统计每天的考勤情况
                $this->workDayJudgement($attendance_params);

            }
        }
        $end = microtime(true);
        echo '[' . date('Y-m-d H:i:s') . ']消耗时间：' . ($end - $start) . "\r\n";
    }

    /**
     *执行考勤的更新和插入
     *
     * @param $current_data
     *
     * @return array
     *
     */
    public function execUpdate($current_data)
    {

        //更新接口
        $return_data = array();
        $current_data['is_statistics'] = 1;
        if (!empty($current_data['id'])) {
            $edit = Api::atom('punch/update_daily_punch_log', $current_data);
            $return_data['edit'] = array('id' => $current_data['id'], 'ret' => $edit);
        } else {
            //插入接口
            $ins = Api::atom('punch/create_daily_punch_log', $current_data);
            $return_data['ins'] = array(
                'id'   => $current_data['staff_id'],
                'date' => $current_data['attendance_date'],
                'ret'  => $ins
            );
        }

        return $return_data;
    }

    /**
     * 判断是否是周六日
     *
     * @param array $params
     *
     * @return bool
     */
    public function saturdayJudgment($params = array())
    {
        $calendar_list = $params['calendar_list'];
        $punch_log = $params['punch_log'];
        $default_punch_info = $params['default_punch_info'];
        $current_date = $params['current_date'];
        if (empty($calendar_list)) {
            if (!empty($punch_log)) {
                //周六日打卡
                $news_staff_info = array('id' => $punch_log['id']);
            } else {
                //周六日 没有上班，也要记录一下，方便统计
                $news_staff_info = $default_punch_info;
                $news_staff_info['attendance_date'] = $current_date;
            }
            $news_staff_info['approval_half'] = 9;
            $news_staff_info ['is_statistics'] = 1;
            $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
            //数据保存到数据库

            $this->return_statis[] = $this->execUpdate($news_staff_info);

            return true;
        }

        return false;
    }

    /**
     * 判断节假日
     *
     * @param array $params
     *
     * @return bool
     */
    public function holidayJudgment($params = array())
    {

        $calendar_list = $params['calendar_list'];
        $punch_log = $params['punch_log'];
        $default_punch_info = $params['default_punch_info'];
        $current_date = $params['current_date'];

        if (!empty($calendar_list) && $calendar_list['type'] == 2) {
            if (!empty($punch_log)) {
                //节假日打卡
                $news_staff_info = array('id' => $punch_log['id']);
            } else {
                //节假日未打卡
                $news_staff_info = $default_punch_info;
                $news_staff_info['attendance_date'] = $current_date;
            }
            $news_staff_info['approval_half'] = 8;
            $news_staff_info ['is_statistics'] = 1;
            $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');

            $this->return_statis[] = $this->execUpdate($news_staff_info);

            return true;
        }

        return false;
    }


    /**
     * 工作日判断
     *
     * @param array $params
     */
    public function workDayJudgement($params = array())
    {

        $punch_log = $params['punch_log'];
        $default_punch_info = $params['default_punch_info'];
        $current_date = $params['current_date'];
        $last_punch_time = $params['last_punch_time'];
        $first_punch_time = $params['first_punch_time'];
        $afternoon_end = $params['afternoon_end'];
        $morning_start = $params['morning_start'];
        $news_staff_info = array('late_time' => 0, 'early_time' => 0);
        if (!empty($punch_log)) {
            if (!empty($first_punch_time) && ($first_punch_time > $morning_start)) {//迟到
                $news_staff_info ['late_time'] = ($first_punch_time - $morning_start);
            } else {
                $news_staff_info ['late_time'] = 0;
            }

            if (!empty($last_punch_time) && ($last_punch_time < $afternoon_end)) { //早退
                $news_staff_info ['early_time'] = ($afternoon_end - $last_punch_time);
            } else {
                $news_staff_info ['early_time'] = 0;
            }
            $news_staff_info ['id'] = $punch_log['id'];
        } else {//当天考勤缺失，插入数据库记录一下
            $news_staff_info = array(
                'abnormal_state'  => 3, //考勤确实
                'attendance_date' => $current_date
            );
            $news_staff_info = array_merge($news_staff_info, $default_punch_info);
        }

        //表示考勤统计状态和考勤时间
        $news_staff_info ['is_statistics'] = 1;
        $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
        $this->return_statis[] = $this->execUpdate($news_staff_info);
    }


    /**
     * 获取指定人的打卡记录
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getDailyPunchLog($params = array())
    {
        if (empty($params['start_time']) || empty($params['end_time']) || empty($params['user_id'])) {
            return array(1);
        }
        $daily_punch_log = Api::atom('punch/get_daily_punch_log', $params);
        return $daily_punch_log;
    }

    /**
     * 获取需要考勤的人员，以及考勤的规则
     */
    protected function getAttendanceStaff($params = array())
    {
        $params_search = array('all' => 1);
        //获取所有的考勤规则
        $working_rules = Api::atom('/punch/get_working_hours', $params_search);
        if (empty($working_rules)) {
            return array();
        }
        //TODO 做测试
        $params_search['status'] = 1;//所有要考勤的用户
       //$params_search['user_id'] = 164;//所有要考勤的用户
        $working_staff = Api::atom('/punch/get_staff_working_hours', $params_search);
        if (!empty($working_staff)) {
            foreach ($working_staff as &$value) {
                if (isset($value['work_id']) && !empty($value['work_id'])) {
                    $work_id = intval($value['work_id']);
                    $rules = isset($working_rules[$work_id]) ? $working_rules[$work_id] : array();
                    if (empty($rules)) {
                        continue;
                    }
                    $value['morning_start'] = $rules['morning_start'];
                    $value['morning_end'] = $rules['morning_end'];
                    $value['afternoon_start'] = $rules['afternoon_start'];
                    $value['afternoon_end'] = $rules['afternoon_end'];
                    $value['overtime_start'] = $rules['overtime_start'];
                } else {
                    continue;
                }
            }

            return $working_staff;
        }

        return array();
    }


    /**
     * 获取 所有的法定节假日和需要工作的周六日
     *
     * @param array $params
     *
     * @return array
     */
    protected function getWorkingCalendarList($params = array())
    {
        if (empty($params['start_date']) || empty($params['end_date'])) {
            return array();
        }
        $working_calendar_list = Api::atom('hr_leave/working_calendar_list', $params);

        if (!empty($working_calendar_list)) {
            $after_sourt_list = array();
            foreach ($working_calendar_list as $working_list) {
                $after_sourt_list[$working_list['date']]['type'] = $working_list['type'];
            }

            return $after_sourt_list;
        }

        return array();
    }


}
