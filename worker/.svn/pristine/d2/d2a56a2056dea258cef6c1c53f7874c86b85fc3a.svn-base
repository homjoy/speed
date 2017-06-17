<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');

/**
 *  统计考勤数据
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-11-10
 */
class StatisticsAttendance extends \Frame\Script {

    protected $params = array();
    protected $errors = array();
    protected $page_size = 100;

    public function run() {
        //1.获取用户的数据
        $args = $this->request->arg ['arg'];
        $start = microtime(true);
        if (!empty($args[0])) {
            $start_date = $args[0];
        } else {
            $start_date = date('Y-m-01');
        }
        if (!empty($args[1])) {
            $end_date = $args[1];
        } else {
            $end_date = date('Y-m-t');
        }

        //考勤天数统计
        $num = $this->daysbetweendates($start_date, $end_date);
        if (!empty($num)) {
            $num = intval($num);
        } else {
            $num = 1;
        }
        var_dump($num);
        exit;
        //获取所有的用户和考勤规则
        $attendance_staff = $this->getAttendanceStaff();
        //获取所有的请假信息
        $leave_params = array(
            'start_date' => $start_date, 'end_date' => $end_date
        );
        $leave_list = $this->getLeaveList($leave_params);
        //获取节假日和工作日
        $working_params = array(
            'start_date' => $start_date, 'end_date' => $end_date
        );
        $working_calendar_list = $this->getWorkingCalendarList($working_params);

        if (!empty($attendance_staff)) {
            $start_date_time = strtotime($start_date);
            //循环需要考勤的人
            foreach ($attendance_staff as $staff) {
                //临时的数组
                $tmp_staff_info = array();
                //获取打卡记录的参数
                $punch_log_prams = array(
                    'user_id' => $staff['user_id'],
                    'start_time' => $start_date,
                    'end_time' => $end_date,
                    'all' => 1,
                );
                //所有当前人的，当前月的打卡记录
                $all_punch_log = $this->getDailyPunchLog($punch_log_prams);
                //当前人的请假记录
                $current_staff_leave = isset($leave_list[$staff['user_id']]) ? $leave_list[$staff['user_id']] : '';
                ;
                //循环考勤天数
                for ($i = 0; $i < $num; $i++) {
                    $news_staff_info = array();
                    $current_date = date('Y-m-d', strtotime($i . 'days', $start_date_time));
                    //如果当前没有，则保存进入数据库，默认的数据
                    $default_daily_punch_info = array(
                        'user_id' => $staff['user_id'],
                        'staff_id' => $staff['staff_id'],
                        'name_cn' => $staff['name_cn'],
                        'status' => $staff['status'],
                        'end_time' => '0000-00-00 00:00:00',
                        'start_time' => '0000-00-00 00:00:00',
                    );
                    //统计考勤的时间，这个人的打卡信息
                    if (!empty($all_punch_log[$current_date])) {
                        //当前人，指定日期的打卡记录
                        $current_date_punch_log = $all_punch_log[$current_date];
                        //最后一次打卡时间
                        if ($current_date_punch_log['end_time'] == '0000-00-00 00:00:00') {
                            $last_punch_time = 0;
                        } else {
                            $last_punch_time = strtotime($current_date_punch_log['end_time']);
                        }
                        //晚上考勤时间标准
                        $afternoon_end = strtotime($current_date . ' ' . trim($staff['afternoon_end']));
                        //第一次打卡时间
                        if ($current_date_punch_log['start_time'] == '0000-00-00 00:00:00') {
                            $first_punch_time = 0;
                        } else {
                            $first_punch_time = strtotime($current_date_punch_log['start_time']);
                        }
                        //早上考勤时间标准
                        $morning_start = strtotime($current_date . ' ' . trim($staff['morning_start']));
                    } else {
                        $current_date_punch_log = array();
                    }

                    //正常上班1上午请假 2下午请假3全天请假 6 节假日未打卡 7 周六日未打卡 8 节假日打卡9 周六日打卡
                    //1.判断休息日
                    $week = date('w', strtotime($current_date));
                    //是 周六日 且 没有在 串休表中 即 type = 2
                    if ($week == 0 || $week == 6) {
                        //如果 这一天是周六日，并且 在假期表中，不是串休的，直接返回
                        if (empty($working_calendar_list[$current_date])) {
                            if (!empty($current_date_punch_log)) {
                                //周六日打卡
                                $news_staff_info = array(
                                    'approval_half' => '9', //星期天
                                    'id' => $current_date_punch_log['id']
                                );
                            } else {
                                //周六日未打卡
                                $news_staff_info = $default_daily_punch_info;
                                $news_staff_info['attendance_date'] = $current_date;
                                $news_staff_info['approval_half'] = 9; //星期天打卡
                            }
                            $news_staff_info ['is_statistics'] = 1;
                            $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
                            $tmp_staff_info[$current_date] = $news_staff_info;
                            continue;
                        }
                    }

                    //2. 是法定节假日
                    if (!empty($working_calendar_list[$current_date]) && $working_calendar_list[$current_date]['type'] == 2) {
                        if (!empty($current_date_punch_log)) {
                            //节假日打卡
                            $news_staff_info = array('approval_half' => 8,
                                'id' => $current_date_punch_log['id']
                            );
                        } else {
                            //节假日未打卡
                            $news_staff_info = $default_daily_punch_info;
                            $news_staff_info['attendance_date'] = $current_date;
                            $news_staff_info['approval_half'] = 8;
                        }
                        $news_staff_info ['is_statistics'] = 1;
                        $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
                        $tmp_staff_info[$current_date] = $news_staff_info;
                        continue;
                    }

                    //3.判断是否请假 ,请假做了上午和下午的处理
                    if (!empty($current_staff_leave)) {
                        //上午请假信息
                        $am_approval = !empty($current_staff_leave[$current_date . '-1']) ?
                                $current_staff_leave[$current_date . '-1'] : array();
                        //下午请假信息
                        $pm_approval = !empty($current_staff_leave[$current_date . '-2']) ?
                                $current_staff_leave[$current_date . '-2'] : array();
                        //全天请假
                        if (!empty($am_approval) || !empty($pm_approval)) {
                            if (!empty($am_approval) && !empty($pm_approval)) {

                                $news_staff_info = array('attendance_date' => $current_date, 'approval_half' => 3,
                                    'approval_am_type' => $am_approval['absence_type'],
                                    'approval_pm_type' => $pm_approval['absence_type'],
                                    'approval_am_status' => $am_approval['status'],
                                    'approval_pm_status' => $pm_approval['status'],);
                                $news_staff_info = array_merge($news_staff_info, $default_daily_punch_info);
                                //判断是否请了假，也打卡
                                if (!empty($current_date_punch_log)) {
                                    $news_staff_info['id'] = $current_date_punch_log['id'];
                                }
                            } else {
                                if (!empty($pm_approval)) { //下午请假
                                    $news_staff_info = array('attendance_date' => $current_date,
                                        'approval_half' => 1,
                                        'approval_am_type' => $am_approval['absence_type'],
                                        'approval_am_status' => $am_approval['status'],);
                                    //判断请半天假，是否打卡
                                    if (!empty($current_date_punch_log)) {
                                        //判断一下，上午请假，下午是否早退
                                        if (!empty($last_punch_time) && ($last_punch_time < $afternoon_end)) {
                                            $news_staff_info['early_time'] = $afternoon_end - $last_punch_time;
                                        } else {
                                            $news_staff_info['early_time'] = 0;
                                        }
                                        $news_staff_info['id'] = $current_date_punch_log['id'];
                                    } else {
                                        $news_staff_info = array_merge($news_staff_info, $default_daily_punch_info);
                                        $news_staff_info['abnormal_state'] = 2; //考勤异常，下午请假但上午午未打卡
                                    }
                                } else if (!empty($am_approval)) { //上午请假
                                    $news_staff_info = array(
                                        'attendance_date' => $current_date,
                                        'approval_half' => 2,
                                        'approval_pm_type' => $pm_approval['absence_type'],
                                        'approval_pm_status' => $pm_approval['status'],
                                    );
                                    //判断请半天假，是否打卡
                                    if (!empty($current_date_punch_log)) {
                                        //判断一下，下午请假，上午是否迟到
                                        if (!empty($first_punch_time) && ($first_punch_time > $morning_start)) {
                                            $news_staff_info['late_time'] = $first_punch_time - $morning_start;
                                        } else {
                                            $news_staff_info['late_time'] = 0;
                                        }
                                        $news_staff_info['id'] = $current_date_punch_log['id'];
                                    } else {
                                        $other_info = array(
                                            'user_id' => $staff['user_id'],
                                            'staff_id' => $staff['staff_id'],
                                            'name_cn' => $staff['name_cn'],
                                            'status' => 0,
                                            'end_time' => '0000-00-00 00:00:00',
                                            'start_time' => '0000-00-00 00:00:00',
                                            'abnormal_state' => 1, //上午请假但下午未打卡
                                            'attendance_date' => $current_date
                                        );
                                        $news_staff_info = array_merge($news_staff_info, $other_info);
                                    }
                                }
                            }

                            $tmp_staff_info[$current_date] = $news_staff_info;
                            continue;
                        }
                    }

                    //4.统计每天的考勤情况

                    if (!empty($current_date_punch_log)) {
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
                        $news_staff_info ['id'] = $current_date_punch_log['id'];
                    } else {//当天考勤缺失，插入数据库记录一下
                        $news_staff_info = array(
                            'user_id' => $staff['user_id'],
                            'staff_id' => $staff['staff_id'],
                            'name_cn' => $staff['name_cn'],
                            'status' => 1,
                            'end_time' => '0000-00-00 00:00:00',
                            'start_time' => '0000-00-00 00:00:00',
                            'abnormal_state' => 3, //上午请假，但是一天没有打卡记录
                            'attendance_date' => $current_date
                        );
                    }
                    //表示考勤统计状态和考勤时间
                    $news_staff_info ['is_statistics'] = 1;
                    $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
                    $tmp_staff_info[$current_date] = $news_staff_info;
                }
                //进行更新或者插入处理
                foreach ($tmp_staff_info as $tmp_current_data) {
                    if (!empty($tmp_current_data)) {
                        //更新接口
                        if (!empty($tmp_current_data['id'])) {
                            $edit = $return_data = APi::atom('punch/update_daily_punch_log', $tmp_current_data);
                            $this->app->log->log('crontab/statistics_attendance.log', "[DEBUG-u]:[status:{$edit}]" . json_encode($tmp_current_data));
                        } else {
                            //插入接口
                            $ins = $return_data = APi::atom('punch/create_daily_punch_log', $tmp_current_data);
                            $this->app->log->log('crontab/statistics_attendance.log', "[DEBUG-i]:[status:{$ins}]" . json_encode($tmp_current_data));
                        }
                    } else {
                        echo '无数据更新';
                    }
                }
            }
        }
    }

    /**
     * @param $staff
     * @param $current_data
     */
    protected function statisticsWorking($staff, $current_date) {
        
    }

    /**
     * 获取两个时间之间的间隔
     * @param $date1
     * @param $date2
     *
     * @return float
     */
    public function daysbetweendates($date1, $date2) {
        if($data1 > $data2){
            return false;
        }
        $date1 = strtotime(date('Y-m-d 00:00:00', strtotime($date1)));
        $date2 = strtotime(date('Y-m-d 23:59:59', strtotime($date2)));
        $days = ceil(abs($date1 - $date2) / 86400);
        return $days;
    }

    /**
     * 获取指定人的打卡记录
     * @param array $params
     *
     * @return mixed
     */
    public function getDailyPunchLog($params = array()) {
        if (empty($params['start_time']) || empty($params['end_time']) || empty($params['user_id']))
            return array();
        $daily_punch_log = APi::atom('punch/get_daily_punch_log', $params);
        $new_daily_punch_log = array();
        foreach ($daily_punch_log as $key => $value) {
            $new_daily_punch_log[$value['attendance_date']] = $value;
        }
        return $new_daily_punch_log;
    }

    /**
     * 获取需要考勤的人员，以及考勤的规则
     */
    protected function getAttendanceStaff() {
        $params = array(
            'all' => 1,
        );
        //获取所有的考勤规则
        $working_rules = APi::atom('punch/get_working_hours', $params);
        if (empty($working_rules))
            return array();
        $working_staff = APi::atom('punch/get_staff_working_hours', $params);
        if (!empty($working_staff)) {
            foreach ($working_staff as &$value) {
                if (isset($value['work_id']) && !empty($value['work_id'])) {
                    $work_id = intval($value['work_id']);
                    $rules = isset($working_rules[$work_id]) ? $working_rules[$work_id] : array();
                    if (empty($rules))
                        continue;
                    $value['morning_start'] = $rules['morning_start'];
                    $value['morning_end'] = $rules['morning_end'];
                    $value['afternoon_start'] = $rules['afternoon_start'];
                    $value['afternoon_end'] = $rules['afternoon_end'];
                    $value['overtime_start'] = $rules['overtime_start'];
                }else {
                    continue;
                }
            }
            return $working_staff;
        }
        return array();
    }

    /**
     * 读取一个时间段内的所有的请假信息
     */
    protected function getLeaveList($params = array()) {
        if (empty($params['start_date']) || empty($params['end_date'])) {
            return array();
        }
        $params['all'] = 1;
        $params['status'] = array(1, 2, 3, 4);
        $leave_list_info = APi::atom('hr_leave/get_leave_list', $params);

        //整理后的数据

        if (!empty($leave_list_info)) {
            //将请假的记录整理为 人=>日期->请假类型关系表
            $after_sortout_list = array();
            foreach ($leave_list_info as $leave_info) {
                $length = ceil((strtotime($leave_info['end_date'] . ' 23:59:59') - strtotime($leave_info['start_date']
                                . ' 00:00:00')) / 86400);
                for ($i = 0; $i < $length; $i++) {
                    $start_date = strtotime($leave_info['start_date']);
                    //如果 start_half 为空或者为am 则上午请假，如果为pm 则下午请假
                    $date_time = date('Y-m-d', strtotime($i . ' days', $start_date));
                    //上午请假，标记为1
                    //如果开始时间和当前计算的时间相等，则是请假的第一天
                    if ($leave_info['start_date'] == $date_time) {
                        $key = $leave_info['user_id'];
                        //如果请假的开始表示为空或者 等于AM 则上午请假
                        if (empty($leave_info['start_half']) || strtoupper($leave_info['start_half']) == 'AM') {
                            $after_sortout_list[$key][$date_time . '-1'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                        } else if (!empty($leave_info['start_half']) && strtoupper($leave_info['start_half']) == 'PM') {
                            //如果开始的标示不为空，且=PM 则是下午请假
                            $after_sortout_list[$key][$date_time . '-2'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                        }
                        //如果当前时间小于 结束时间，则标示下午也请假
                        if ($date_time < $leave_info['end_date']) {
                            if (empty($leave_info['start_half']) || strtoupper($leave_info['start_half']) == 'AM') {
                                $after_sortout_list[$key][$date_time . '-2'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                            }
                        }
                    }

                    //如果结束时间和当前的时间相等,请假的最后一天
                    if ($leave_info['end_date'] == $date_time) {
                        //如果结束表示为空或者结束表示 =PM 则标示下午请假
                        if (empty($leave_info['end_half']) || strtoupper($leave_info['end_half']) == 'PM') {
                            $after_sortout_list[$key][$date_time . '-2'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                        } if (!empty($leave_info['end_half']) && strtoupper($leave_info['end_half']) == 'AM') { //上午请假
                            $after_sortout_list[$key][$date_time . '-1'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                        }
                    }
                    //中间有多天请假，即使周六日也表示为请假
                    if ($date_time > $leave_info['start_date'] && $date_time < $leave_info['end_date']) {
                        $after_sortout_list[$key][$date_time . '-1'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                        $after_sortout_list[$key][$date_time . '-2'] = array('status' => $leave_info['status'], 'absence_type' => $leave_info['absence_type']);
                    }
                }
            }

            return $after_sortout_list;
        }
        return array();
    }

    /**
     * 获取 所有的法定节假日和需要工作的周六日
     * @param array $params
     *
     * @return array
     */
    protected function getWorkingCalendarList($params = array()) {
        if (empty($params['start_date']) || empty($params['end_date'])) {
            return array();
        }
        $working_calendar_list = APi::atom('hr_leave/working_calendar_list', $params);
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
