<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;
use Pixie\Exception;

date_default_timezone_set('Asia/Shanghai');

/**
 *  保存们禁卡系统中的 staff_id 00000001 和 speed系统 staff_id A00001 的对应关系
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class SavePunchStaffRelation extends \Frame\Script
{

    protected $params = array();
    protected $errors = array();
    protected $page_size = 20;

    public function run()
    {
        $args = $this->request->arg['arg'];
        if (!empty($args[0])) {
            $update_time = $args[0];
        } else {
            $update_time = date('Y-m-d 00:00:00', strtotime('-1 day'));
        }
        if (!empty($args[1])) {
            $end_time = $args[1];
        } else {
            $end_time = date('Y-m-d 23:59:59', strtotime('-1 day'));
        }
        //$update_time ='2009-01-01 00:00:00';
        //$end_time =date('Y-m-d 23:59:59');
        $params = array(
            'status'      => array(1, 2, 3), //所后的status 数据
            'update_time' => $update_time,
            'end_time'    => $end_time
        );
        $total_params = $params;
        $total_params['count'] = 1;
        $total = Api::atom('account/get_user_info', $total_params);
        echo '[' . date('Y-m-d H:i:s') . '] 共获取' . $total . "\r\n";
        if ($total <= 0) {
            //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '没有需要同步的用户信息');
            exit;
        }

        $page_num = ceil($total / $this->page_size);
        echo '[' . date('Y-m-d H:i:s') . '] 共获取' . $page_num . "页数\r\n";

        $params['limit'] = $this->page_size;
        for ($i = 0; $i < $page_num; $i++) {
            $params['offset'] = ($i * $this->page_size);
            $get_user_info = Api::atom('account/get_user_info', $params);

            //var_dump($get_user_info);
            if (!empty($get_user_info)) {
                $this->sycWorkingHours($get_user_info);
                $this->sysPunchStaffRelation($get_user_info);
            }
            usleep(1000);
        }
        echo '[' . date('Y-m-d H:i:s') . '] END';
    }

    /**
     * 获取对应关系是否存在，通过user_id 获取，存在，则执行update 否则 执行更新
     *
     * @param array $params
     *
     * @return bool
     */
    public function getPunchStaffRelation($params = array())
    {
        return true;
    }

    /**
     * 将数据同步到 working_hours_staff_relation 表中
     *  同步,新增加的用户,部门修改,用户离职
     */
    public function sycWorkingHours($user_info)
    {
        $user_ids = array_keys($user_info);
        $working_staff_relation = Api::atom('punch/get_staff_working_hours', array('user_id' => implode(',', $user_ids)));
        //更新数据
        if (!empty($working_staff_relation)) {
            foreach ($working_staff_relation as $working_key => $working_value) {
                $tmp_user_info = $user_info[$working_value['user_id']];
                $update_tmp_value = array(
                    //'user_id' => $tmp_user_info['user_id'],
                    'name_cn'   => $tmp_user_info['name_cn'],
                    'id'        => $working_value['id'],
                    'staff_id'  => $tmp_user_info['staff_id'],
                    'depart_id' => $tmp_user_info['depart_id'],

                    'work_id'   => $working_value['work_id']
                );
                if($tmp_user_info['status'] ==2 ){
                    $update_tmp_value['status'] = 2;
                }else{
                    $update_tmp_value['status']   = $working_value['status'];
                }
                try {
                    $return = Api::atom('punch/update_working_staff_hours', $update_tmp_value);
                    //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '更新返回：' . json_encode($return));
                    echo '[' . date('Y-m-d H:i:s') . ']' . $working_value['user_id'] . $working_value['name_cn'] . ' 更新考勤表返回：'
                        . json_encode($return) . PHP_EOL;
                    unset($user_info[$working_value['user_id']]);
                } catch (\Exception $e) {
                    echo '[' . date('Y-m-d H:i:s') . ']' . $working_value['user_id'] . $working_value['name_cn'] . '新增考勤数据返回 出错：'
                        . $e->getMessage() . PHP_EOL;
                }
            }
        }

        //新增数据
        if (!empty($user_info)) {
            foreach ($user_info as $user_key => $user_value) {
                if ($user_value['status'] == 2) {
                    continue;
                }
                $tmp_value = array(
                    'user_id'   => $user_value['user_id'],
                    'name_cn'   => $user_value['name_cn'],
                    'staff_id'  => $user_value['staff_id'],
                    'status'    => 3, //默认不进行考勤
                    'work_id'   => 0,
                    'depart_id' => $user_value['depart_id'],

                );
                try {
                    $return = Api::atom('punch/create_working_staff_hours', $tmp_value);
                    //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '新增返回：' . json_encode($return));
                    echo '[' . date('Y-m-d H:i:s') . '] ' . $user_value['user_id'] . $user_value['name_cn'] . '  新增考勤数据返回：'
                        . json_encode($return) . PHP_EOL;
                } catch (\Exception $e) {
                    echo '[' . date('Y-m-d H:i:s') . ']' . $user_value['user_id'] . $user_value['name_cn'] . '  新增考勤数据返回 出错：'
                        . $e->getMessage() . PHP_EOL;
                }
            }
        }
    }

    /**
     * 数据同步到 punch_staff_relation 表中
     *
     * @param $user_info
     *
     *
     *
     * @throws \Frame\Speed\Exception\ApiException
     */
    public function sysPunchStaffRelation($user_info)
    {
        $user_ids = array_keys($user_info);
        $punch_staff_relation = Api::atom('punch/get_punch_staff_relation', array('user_id' => implode(',', $user_ids)));
        //更新数据

        if (!empty($punch_staff_relation)) {

            foreach ($punch_staff_relation as $staff_key => $staff_value) {
                $tmp_user_info = $user_info[$staff_key];

                $update_tmp_value = array(
                    'user_id'  => $tmp_user_info['user_id'],
                    'name_cn'  => $tmp_user_info['name_cn'],
                    'staff_id' => $tmp_user_info['staff_id'],
                    'status'   => $tmp_user_info['status'],
                );
                if (strlen($tmp_user_info['staff_id']) >= 6) {
                    $update_tmp_value['punch_staff_id'] = str_pad(substr($tmp_user_info['staff_id'], 1), 9, 0, STR_PAD_LEFT);
                } else {
                    $update_tmp_value['punch_staff_id'] = '000000000';
                }
                try {
                    $return = Api::atom('punch/update_punch_staff_relation', $update_tmp_value);

                    //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '更新返回：' . json_encode($return));
                    echo '[' . date('Y-m-d H:i:s') . ']' . $tmp_user_info['name_cn'] . '  更新返回：' . json_encode($return) . PHP_EOL;
                    unset($user_info[$staff_key]);
                }catch (\Exception $e){
                    echo '[' . date('Y-m-d H:i:s') . ']' . $tmp_user_info['name_cn'] . '  更新失败：' . $e->getMessage(). PHP_EOL;
                    unset($user_info[$staff_key]);
                }
            }
        }

        //新增数据
        if (!empty($user_info)) {
            foreach ($user_info as $user_key => $user_value) {
                $tmp_value = array(
                    'user_id'  => $user_value['user_id'],
                    'name_cn'  => $user_value['name_cn'],
                    'staff_id' => $user_value['staff_id'],
                    'status'   => $user_value['status'],
                );

                if (strlen($user_value['staff_id']) >= 6) {
                    $tmp_value['punch_staff_id'] = str_pad(substr($user_value['staff_id'], 1), 9, 0, STR_PAD_LEFT);
                } else {
                    $tmp_value['punch_staff_id'] = '000000000';
                }
                try {
                    $return = Api::atom('punch/create_punch_staff_relation', $tmp_value);
                    //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '新增返回：' . json_encode($return));
                    echo '[' . date('Y-m-d H:i:s') . ']' . $user_value['name_cn'] . '   新增返回：' . json_encode($return) . PHP_EOL;
                }catch(\Exception $e){
                    echo '[' . date('Y-m-d H:i:s') . ']' . $user_value['name_cn'] . '   新增返回失败：' . $e->getMessage(). PHP_EOL;
                    unset($user_info[$user_key]);
                }
            }
        }
    }
}
