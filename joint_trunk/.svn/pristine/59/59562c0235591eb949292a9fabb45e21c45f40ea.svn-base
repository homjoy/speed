<?php

namespace Joint\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');

/**
 *  保存们禁卡系统中的 staff_id 00000001 和 speed系统 staff_id A00001 的对应关系
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class SavePunchStaffRelation extends \Frame\Script {

    protected $params = array();
    protected $errors = array();
    protected $page_size = 20;

    public function run() {
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

        $params = array(
            'status' => array(1, 2, 3), //所后的status 数据
            'update_time' => $update_time,
            'end_time' => $end_time);
        $total_params = $params;
        $total_params['count'] = 1;
        $total = APi::atom('account/get_user_info', $total_params);
        echo '['.date('Y-m-d H:i:s').'] 共获取'.$total."\r\n";
        if ($total <= 0) {
            //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '没有需要同步的用户信息');
            exit;
        }
        $page_num = ceil($total / $this->page_size);
        echo '['.date('Y-m-d H:i:s').'] 共获取'.$page_num."页数\r\n";

        $params['limit'] = $this->page_size;
        for ($i = 0; $i < $page_num; $i++) {
            $params['offset'] = ($i * $this->page_size);
            $user_info = APi::atom('account/get_user_info', $params);

            if (!empty($user_info)) {
                $user_ids = array_keys($user_info);
                $punch_staff_relation = APi::atom('punch/get_punch_staff_relation', array('user_id' => $user_ids));
                //更新数据
                if (!empty($punch_staff_relation)) {
                    foreach ($punch_staff_relation as $staff_key => $staff_value) {
                        $tmp_user_info = $user_info[$staff_key];
                        $update_tmp_value = array(
                            'user_id' => $tmp_user_info['user_id'],
                            'name_cn' => $tmp_user_info['name_cn'],
                            'staff_id' => $tmp_user_info['staff_id'],
                            'status' => $tmp_user_info['status'],
                        );
                        if (strlen($tmp_user_info['staff_id']) >= 6) {
                            $update_tmp_value['punch_staff_id'] = str_pad(substr($tmp_user_info['staff_id'], 1), 9, 0, STR_PAD_LEFT);
                        } else {
                            $update_tmp_value['punch_staff_id'] = '000000000';
                        }
                       
                        $return = APi::atom('punch/update_punch_staff_relation', $update_tmp_value);
                        //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '更新返回：' . json_encode($return));
                        echo '['.date('Y-m-d H:i:s').'] 更新返回：' . json_encode($return);
                        unset($user_info[$staff_key]);
                    }
                }

                //新增数据
                if (!empty($user_info)) {
                    foreach ($user_info as $user_key => $user_value) {
                        $tmp_value = array(
                            'user_id' => $user_value['user_id'],
                            'name_cn' => $user_value['name_cn'],
                            'staff_id' => $user_value['staff_id'],
                            'status' => $user_value['status'],
                        );
                        
                        if (strlen($user_value['staff_id']) >= 6) {
                            $tmp_value['punch_staff_id'] = str_pad(substr($user_value['staff_id'], 1), 9, 0, STR_PAD_LEFT);
                        } else {
                            $tmp_value['punch_staff_id'] = '000000000';
                        }
                       
                        $return = APi::atom('punch/create_punch_staff_relation', $tmp_value);
                        //$this->app->log->log('crontab/punch_syn_punch_staff_relation', '新增返回：' . json_encode($return));
                        echo '['.date('Y-m-d H:i:s').'] 新增返回：' . json_encode($return);
                    }
                }
            }
            usleep(1000);
        }
        echo '['.date('Y-m-d H:i:s').'] END';
    }

    /**
     * 获取对应关系是否存在，通过user_id 获取，存在，则执行update 否则 执行更新
     * @param array $params
     *
     * @return bool
     */
    public function getPunchStaffRelation($params = array()) {
        return true;
    }

}
