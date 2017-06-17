<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15-11-17
 * Time: 上午11:57
 */

namespace Admin\Modules\Hr\Attendance;

use Admin\Package\Account\UserInfo;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Attendance\Attendance;
use Admin\Package\Attendance\PunchLog;
use Admin\Package\Attendance\WorkingHours;
use Admin\Package\Hr_leave\LeaveList;
use Admin\Package\Hr_leave\WorkingCalendarList;
/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  PersonalLeave
 * Date: 15/11/13
 * Time: 下午12:18
 */
set_time_limit(1200);

class AjaxAttendanceStatistics extends BaseModule
{

    protected $errors = null;
    private $params = null;
    //protected $checkUserPermission = true;
    protected $return_statis = array(); //整理后数据
    public static $VIEW_SWITCH_JSON = true;

    public function run()
    {
        $this->_init();
        //1.获取用户的数据
        //TODO 备注
        $start_date = $this->params['attendance_start_date'];
        $end_date = $this->params['attendance_end_date'];
        
        if ($start_date > $end_date) {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '开始不能比结束时间大');

            return $this->app->response->setBody($return);
        }
        if (empty($start_date) || empty($end_date)) {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '开始时间和结束时间不能为空');

            return $this->app->response->setBody($return);
        }
        //考勤天数统计
        $leave_params = array(
            'gt_start_date' => $start_date,
            'lt_end_date'   => $end_date
        );
        //获取请假记录
        $leave_list = $this->getLeaveList($leave_params);

        //获取所有的考勤规则
        $working_rule_search = array(
            'all' => 1
        );

        $working_rules = WorkingHours::getInstance()->getWorkingHours($working_rule_search);
        $return_attendance_rule = array();
        if (!empty($leave_list)) {
            $user_ids = array_keys($leave_list);
            $user_params = array(
                'status' => array(1,2,3),
                'user_id' =>implode(',',$user_ids),
                'all' =>1
            );
            $user_info_list =UserInfo::getInstance()->getUserInfo($user_params);

            //循环需要考勤的人
            foreach ($leave_list as $user_key => $leave_info) {
                $current_user_info = !empty( $user_info_list[$user_key] ) ? $user_info_list[$user_key]:array();

                if(empty($current_user_info)) continue;
                //获取考勤规则
                //获取所有的用户和考勤规则
                $att_params = array(
                    'user_id' => $user_key,
                    'status'  => 1
                );
                $attendance_work_info= $this->getAttendanceStaff($att_params,$working_rules);
                if(empty($attendance_work_info)) continue;
                foreach ($leave_info as $leave_key => $leave_data) {

                    $leave_return = $this->leaveJudgement($leave_data, $leave_key, $current_user_info,$attendance_work_info);
                    if(empty($leave_return)){
                        $return_attendance_rule[$user_key][$leave_key] = $leave_data;
                    }
                }
            }
        }
        if(!empty($return_attendance_rule)){
            $return = Response::gen_error('数据统计成功'.json_encode($return_attendance_rule));
        }else{
            $return = Response::gen_success('数据统计成功');
        }

        return $this->app->response->setBody($return);
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
        if (!empty($current_data['id'])) {
            $edit = PunchLog::getInstance()->updateDailyPunchlog($current_data);
            $return_data['edit'] = array('id' => $current_data['id'], 'ret' => $edit);
        } else {
            //插入接口
            $ins = PunchLog::getInstance()->createDailyPunchLog($current_data);
            $return_data['ins'] = array('id' => $current_data['staff_id'], 'date' => $current_data['attendance_date'], 'ret' => $ins);
        }
        return $return_data;
    }


    /**
     * 请假判断
     *
     * @param array $params
     *
     * @return bool
     */
    public function leaveJudgement($staff_leave = array(), $current_date = '', $current_user_info = array(),$working_rule = array())
    {
        if ( empty($current_date) || empty($current_user_info)) {

            return array();
        }

        //当前员工的考勤规则
        $working_rule = current($working_rule);
        if(empty($working_rule)){
            $afternoon_end = strtotime( $current_date .' 19:00:00');
            $morning_start = strtotime( $current_date .' 10:00:00');
        }else{
            $morning_start = strtotime( $current_date .' '.$working_rule['morning_start']);
            $afternoon_end = strtotime( $current_date .' '.$working_rule['afternoon_end']);
        }
        //定义默认的数据
        $default_punch_info = array(
            'user_id'    => $current_user_info['user_id'],
            'staff_id'   => $current_user_info['staff_id'],
            'name_cn'    => $current_user_info['name_cn'],
            'status'     => $current_user_info['status'],
            'approval_am_type'   => 0, //上午请假类型
            'approval_pm_type'   => 0,// 下午请假类型
            'approval_am_status' => 0,
            'approval_pm_status' => 0,
        );
        //获取用户当天的打卡记录
        $punch_log_params = array(
            'start_time' => $current_date,
            'end_time'   => $current_date,
            'user_id'    => $current_user_info['user_id']
        );
        $punch_log = $this->getDailyPunchLog($punch_log_params);
        $punch_log = current($punch_log);

        if(!empty($punch_log) && $punch_log['end_time']!='0000-00-00 00:00:00' && $punch_log['start_time']!='0000-00-00 00:00:00'  ){
            $last_punch_time = strtotime($punch_log['end_time']);
            $first_punch_time = strtotime($punch_log['start_time']);
        }else{
            $last_punch_time = 0;
            $first_punch_time = 0;
        }
        //上午请假信息
        $am_approval = !empty($staff_leave['am']) ? $staff_leave['am'] : array();
        //下午请假信息
        $pm_approval = !empty($staff_leave['pm']) ? $staff_leave['pm'] : array();
        //全天请假

        if (!empty($am_approval) || !empty($pm_approval)) {
            if (!empty($am_approval) && !empty($pm_approval)) {
                $news_staff_info = array(
                    'attendance_date'    => $current_date,
                    'approval_half'      => 3,
                    'approval_am_type'   => $am_approval['absence_type'], //上午请假类型
                    'approval_pm_type'   => $pm_approval['absence_type'],// 下午请假类型
                    'approval_am_status' => $am_approval['status'],
                    'approval_pm_status' => $pm_approval['status'],
                    'abnormal_state'     => 0,
                    'late_time'          => 0,
                    'early_time'         => 0,

                );
                $news_staff_info = array_merge( $default_punch_info,$news_staff_info);
                //判断是否请了假，也打卡,如果打卡，则更新记录
                if (!empty($punch_log)) {
                    $news_staff_info['id'] = $punch_log['id'];
                }
            } else {
                if (!empty($am_approval)) { //上午请假
                    $news_staff_info = array(
                        'attendance_date'    => $current_date,
                        'approval_half'      => 1,
                        'approval_am_type'   => $am_approval['absence_type'],
                        'approval_am_status' => isset($am_approval['status']) ? $am_approval['status'] : 0,
                        'late_time'          => 0,
                        'early_time'         => 0,
                        'abnormal_state'     => 0
                    );
                    //判断请半天假，是否打卡
                    if (!empty($punch_log)) {
                        //判断一下，上午请假，下午是否早退
                        if (!empty($last_punch_time) && ($last_punch_time < $afternoon_end)) {
                            $news_staff_info['early_time'] = $afternoon_end - $last_punch_time;
                        }
                        $news_staff_info['id'] = $punch_log['id'];
                        $news_staff_info = array_merge($default_punch_info,$news_staff_info);
                    } else {
                        $news_staff_info = array_merge($default_punch_info,$news_staff_info);
                        $news_staff_info['abnormal_state'] = 2; //考勤异常，下午请假但上午午未打卡
                    }
                } else {
                    if (!empty($pm_approval)) { //下午请假
                        $news_staff_info = array(
                            'attendance_date'    => $current_date,
                            'approval_half'      => 2,
                            'approval_pm_type'   => $pm_approval['absence_type'],
                            'approval_pm_status' => isset($pm_approval['status']) ? $pm_approval['status'] : 0,
                            'late_time'          => 0,
                            'early_time'         => 0,
                            'abnormal_state'     => 0
                        );
                        //判断请半天假，是否打卡
                        if (!empty($punch_log)) {
                            //判断一下，下午请假，上午是否迟到
                            if (!empty($first_punch_time) && ($first_punch_time > $morning_start)) {
                                $news_staff_info['late_time'] = $first_punch_time - $morning_start;
                            }
                            $news_staff_info['id'] = $punch_log['id'];
                            $news_staff_info = array_merge($default_punch_info,$news_staff_info);
                        } else {

                            $news_staff_info = array_merge($default_punch_info,$news_staff_info);
                            $news_staff_info['abnormal_state'] = 1;//上午请假但下午未打卡
                        }
                    }
                }
            }
            $news_staff_info ['is_statistics'] = 1;
            $news_staff_info ['statistical_date'] = date('Y-m-d H:i:s');
            $this->return_statis[] = $this->execUpdate($news_staff_info);

            return true;
        }

        return false;
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
            return array();
        }
        $daily_punch_log = PunchLog::getInstance()->getDailyPunchLog($params);
        $new_daily_punch_log = array();
        if (!empty($daily_punch_log)) {
            foreach ($daily_punch_log as $key => $value) {
                $new_daily_punch_log[$value['attendance_date']] = $value;
            }
        }

        return $new_daily_punch_log;
    }


    /**
     * 读取一个时间段内的所有的请假信息
     */
    protected function getLeaveList($params = array())
    {
        if (empty($params['gt_start_date']) || empty($params['lt_end_date'])) {
            return array();
        }
        $params['all'] = 1;
        $params['status'] = array(1, 2, 3, 4);

        //$params['user_id'] = 4161;
        $leave_list_info = LeaveList::getInstance()->getLeaveList($params);

        //整理后的数据
        $calendar_info = array(
            'start_date' => $params['gt_start_date'],
            'end_date'   => $params['lt_end_date'],
        );
        $all_calendar_list = $this->getWorkingCalendarList($calendar_info);

        if (!empty($leave_list_info)) {
            //将请假的记录整理为 人=>日期->请假类型关系表
            $after_sortout_list = array();
            foreach ($leave_list_info as $leave_info) {
                    $key = $leave_info['user_id'];
//                if ($leave_info['end_date'] > $params['lt_end_date']) {
//                    $leave_info['end_date'] = $params['lt_end_date'];
//                }
//                if ($leave_info['start_date'] < $params['gt_start_date']) {
//                    $leave_info['start_date'] = $params['gt_start_date'];
//                }

                $length = ceil((strtotime($leave_info['end_date'] . ' 23:59:59') - strtotime($leave_info['start_date'] . ' 00:00:00'))
                    / 86400);
                for ($i = 0; $i < $length; $i++) {

                    $start_date = strtotime($leave_info['start_date']);
                    //如果 start_half 为空或者为am 则上午请假，如果为pm 则下午请假
                    $date_time = date('Y-m-d', strtotime($i . ' days', $start_date));
                    if (($date_time > $params['lt_end_date'] )||( $date_time < $params['gt_start_date']) ){
                        continue;
                    }
                    //上午请假，标记为1
                    //如果开始时间和当前计算的时间相等，则是请假的第一天
                    if ($leave_info['start_date'] == $date_time) {

                        //如果请假的开始表示为空或者 等于AM 则上午请假
                        if (empty($leave_info['start_half']) || strtoupper($leave_info['start_half']) == 'AM') {
                            $after_sortout_list[$key][$date_time]['am'] = array(
                                'status'       => $leave_info['status'],
                                'absence_type' => $leave_info['absence_type']
                            );
                        } else {
                            if (!empty($leave_info['start_half']) && strtoupper($leave_info['start_half']) == 'PM') {
                                //如果开始的标示不为空，且=PM 则是下午请假
                                $after_sortout_list[$key][$date_time]['pm'] = array(
                                    'status'       => $leave_info['status'],
                                    'absence_type' => $leave_info['absence_type']
                                );
                            }
                        }
                        //如果当前时间小于 结束时间，则标示下午也请假
                        if ($date_time < $leave_info['end_date']) {
                            if (empty($leave_info['start_half']) || strtoupper($leave_info['start_half']) == 'AM') {
                                $after_sortout_list[$key][$date_time]['pm'] = array(
                                    'status'       => $leave_info['status'],
                                    'absence_type' => $leave_info['absence_type']
                                );
                            }
                        }
                    }

                    //如果结束时间和当前的时间相等,请假的最后一天

                    if ($leave_info['end_date'] == $date_time) {
                        //如果结束表示为空或者结束表示 =PM 则标示下午请假
                        if (empty($leave_info['end_half']) || strtoupper($leave_info['end_half']) == 'PM') {
                            $after_sortout_list[$key][$date_time]['pm'] = array(
                                'status'       => $leave_info['status'],
                                'absence_type' => $leave_info['absence_type']
                            );
                            $after_sortout_list[$key][$date_time]['am'] = array(
                                'status'       => $leave_info['status'],
                                'absence_type' => $leave_info['absence_type']
                            );
                        }
                        if ((!empty($leave_info['end_half']) && strtoupper($leave_info['end_half']) == 'AM') ) { //上午请假
                            $after_sortout_list[$key][$date_time]['am'] = array(
                                'status'       => $leave_info['status'],
                                'absence_type' => $leave_info['absence_type']
                            );
                        }
                    }
                    //中间有多天请假，
                    if ($date_time > $leave_info['start_date'] && $date_time < $leave_info['end_date']) {
                        //去除节假日期
                        if (in_array($date_time, $all_calendar_list) && $all_calendar_list[$date_time] == 2) {
                            continue;
                        }
                        //去除周六日,且不在假期表
                        $data_type = date('w', strtotime($date_time));
                        if (($data_type == 0 || $data_type == 6) && empty($all_calendar_list[$date_time])   ) {
                            continue;
                        }
                        $after_sortout_list[$key][$date_time]['am'] = array(
                            'status'       => $leave_info['status'],
                            'absence_type' => $leave_info['absence_type']
                        );
                        $after_sortout_list[$key][$date_time]['pm'] = array(
                            'status'       => $leave_info['status'],
                            'absence_type' => $leave_info['absence_type']
                        );
                    }
                }
            }

            return $after_sortout_list;
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
        $working_calendar_list = WorkingCalendarList::getInstance()->getCalendarList($params);

        if (!empty($working_calendar_list)) {
            $after_sourt_list = array();
            foreach ($working_calendar_list as $working_list) {
                $after_sourt_list[$working_list['date']] = $working_list['type'];
            }

            return $after_sourt_list;
        }

        return array();
    }

    /**
     * 获取需要考勤的人员，以及考勤的规则
     */
    protected function getAttendanceStaff($params = array(),$working_rules = array())
    {
        if (empty($working_rules)) {
            return array();
        }
        //TODO 做测试
        $params_search['status'] = 1;//所有要考勤的用户
        $params_search['user_id'] = $params['user_id'];

        $working_staff = WorkingHours::getInstance()->getStaffWorkingHours($params_search);
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

    private function _init()
    {
        $this->rules = array(
            'search'                => array(//xingming
                   'type' => 'string',
            ),
            'attendance_start_date' => array(
                'type' => 'string',
            ),
            'attendance_end_date'   => array(
                'type' => 'string',
            ),
            'all'                   => array(
                'type'    => 'integer',
                'default' => 0,
            ),
            'staff_id'              => array(
                'type' => 'string',
            ),
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
