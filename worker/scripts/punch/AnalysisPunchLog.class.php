<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');

/**
 *  对打卡日志 按天进行分析
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-11-10
 */
class AnalysisPunchLog extends \Frame\Script {

    protected $params = array();
    protected $errors = array();
    protected $page_size = 50;

    public function run() {
        $args = $this->request->arg ['arg'];
        $start = microtime(true);
//        if (!empty($args[0])) {
//            $update_time = $args[0];
//        } else {
//            $update_time = date('Y-m-d 00:00:00', strtotime('-1 day'));
//        }
//        if (!empty($args[1])) {
//            $end_time = $args[1];
//        } else {
//            $end_time = date('Y-m-d 00:00:00', $_SERVER['REQUEST_TIME']);
//        }

        $update_time = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $end_time = date('Y-m-d 23:59:59', strtotime('-1 day'));
        $search_params = array('start_time' => $update_time, 'end_time' => $end_time);
        $return_info = Api::atom('punch/resolve_daily_punch_log', $search_params);
        //获取导数据之后，保存到数据库中
        if (!empty($return_info)) {
            foreach ($return_info as $key => &$value) {
                unset($value['punch_staff_id']);
                unset($value['update_time']);
                //TODO 测试
                $value['attendance_date'] = date('Y-m-d', strtotime($value['start_time']));
                //$value['attendance_date'] = date('Y-m-d', strtotime('-1 day'));
            }
            unset($value);
            //最好做分段发送
            $num = count($return_info);
            echo '['.date('Y-m-d H:i:s').']分析时间：'.$update_time.'到'.$end_time.',共获取'.$num."\r\n";
            $page_num = ceil($num / $this->page_size);
            for ($j = 0; $j < $page_num; $j++) {
                //有BUG
                $daily_log = array_slice($return_info, $j * $this->page_size, $this->page_size);
                //将分析的数据存入到数据库中 punch_daily_log
                if (!empty($daily_log)) {
                    foreach ($daily_log as $daily_key => $daily_value) {
                        $return_data = Api::atom('punch/create_daily_punch_log', $daily_value);
                        if(intval($return_data) <=0){
                            echo '['.date('Y-m-d H:i:s').']记录数据库失败：#' .json_encode($daily_value) ."\r\n";
                        }
                    }
                    usleep(200000); //休息0.2s
                }
            }
        }
        $end = microtime(true);
        echo '['.date('Y-m-d H:i:s').']消耗时间：' . ($end - $start)."\r\n";
    }

}
