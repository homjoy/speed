<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;
date_default_timezone_set('Asia/Shanghai');
/**
 * 将需要同步的用户数据 保存到对列表
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class SaveUserQueue extends \Frame\Script {

    protected $params = array();
    protected $errors = array();
    protected $page_size = 20;

    public function run() {
        //1.获取用户的数据
        $args = $this->request->arg ['arg'];

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
            'status' => array(1, 2, 3), //所后的status 数据
            //'status' => array(1,3),
            'update_time' => $update_time,
            'end_time' => $end_time
        );
        $total_params = $params;
        $total_params['count'] = 1;
        $total = Api::atom('account/get_user_info', $total_params);
        echo '['.date('Y-m-d H:i:s').']总条数：'.$total."\r\n";
        if($total <=0) {
            echo '['.date('Y-m-d H:i:s').']'.' 无数据'."\r\n";
            exit;
        }
        $page_num = ceil($total/$this->page_size);
        $params['limit'] = $this->page_size;
        for($i = 0;$i < $page_num;$i++) {
            $params['offset'] = ($i*$this->page_size) ;
            $user_info = Api::atom('account/get_user_info', $params);
            if (!empty($user_info)) {
                foreach ($user_info as $user_key => $user_value) {
                    $tmp_value = array(
                        'user_id'   => $user_value['user_id'],
                        'name_cn' => $user_value['name_cn'],
                        'depart_id' => $user_value['depart_id'],
                        //'staff_id' => str_pad(substr($user_value['staff_id'],1),9,0,STR_PAD_LEFT),
                        
                        'hire_time' => $user_value['hire_time'],
                        'status'     =>  $user_value['status'],
                    );
                    if (strlen($user_value['staff_id']) >= 6) {
                        $tmp_value['staff_id'] = str_pad(substr($user_value['staff_id'], 1), 9, 0, STR_PAD_LEFT);
                    } else {
                        $tmp_value['staff_id'] ='99'.str_pad($user_value['staff_id'], 7, 0, STR_PAD_LEFT);
                    }
                    $save_user_info = array('content' => base64_encode(json_encode($tmp_value)), 'channel' => 1, 'status' => 0);
                    $return = Api::atom('punch/create_punch_syn_info', $save_user_info);
                    if(intval($return) <=0){
                        echo '['.date('Y-m-d H:i:s').']'.' 返回值:'.$user_value['user_id'].'|'.json_encode($return)."\r\n";
                    }
                }

            }
        }
        echo '['.date('Y-m-d H:i:s').']'.' END;'."\r\n";

    }

}
