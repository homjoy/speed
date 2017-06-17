<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');
/**
 *  将需要同步的 部门保存到队列
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class SaveDepartQueue extends \Frame\Script
{

    protected $page_size = 20;

    public function run() {
        //1.获取部门的数据
        $args = $this->request->arg['arg'];
        if(!empty($args[0])){
            $update_time = $args[0];
        }else{
            $update_time = date('Y-m-d H:i:s', strtotime('-10 minute'));
        }
        if(!empty($args[1])){
            $end_time = $args[1];
        }else{
            $end_time = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }
        $params = array(
            'status' => array(1, 0), //所有的status 数据
            //'status' => array(1),
            'update_time' => $update_time,
            'end_time' => $end_time
        );
        $total_params = $params;
        $total_params['count'] = 1;
        $total = Api::atom('department/depart_info_list', $total_params);
    
        if($total <=0) {
            echo '['.date('Y-m-d H:i:s').']'.' 无数据'."\r\n";
            exit;
        }
        $page_num = ceil($total/$this->page_size);
        $params['limit'] = $this->page_size;
        for($i = 0;$i < $page_num;$i++) {
            $params['offset'] = ($i*$this->page_size) ;
            $depart_info = Api::atom('department/depart_info_list', $params);
            //循环所有数据
            if (!empty($depart_info)) {
                foreach ($depart_info as $depart_key => $depart_value) {
                    $tmp_value = array(
                        'depart_id'   => $depart_value['depart_id'],
                        'depart_name' => $depart_value['depart_name'],
                        'parent_id'   => $depart_value['parent_id'],
                        'depart_info' => $depart_value['depart_info'],
                        'status'     =>  $depart_value['status'],
                        );
                    $save_depart_info = array('content' => base64_encode(json_encode($tmp_value)), 'channel' => 2, 'status' => 0);
                    $return = Api::atom('punch/create_punch_syn_info',$save_depart_info);
                    if(intval($return) <=0){
                        echo '['.date('Y-m-d H:i:s').']'.' 返回值:'.$depart_value['depart_id'].'|'.$return."\r\n";
                    }
                    //$this->app->log->log('crontab/punch_save_depart_queue_return', json_encode($return));
                }

            }
        }
        echo '['.date('Y-m-d H:i:s').']'.' END;'."\r\n";
    }

}
