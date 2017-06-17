<?php

namespace Worker\Scripts\Message_send;

use Worker\Package\Notification\Notification;
use Frame\Speed\Lib\Api;
use Libs\Util\ArrayUtilities;

date_default_timezone_set('Asia/Shanghai');

/**
 * 定时任务,读取administration 中的待发送的消息.
 * Class
 *
 * @package Worker\Scripts\Notification
 * edit by guojiezhu 2016-02-29 优化程序
 */
class MessageSend extends \Frame\Script {

    protected $send_num = 200; //一次读取的条数

    public static $channelMap = array(
        '1' => 'mail',
        '2' => 'sms',
        '3' => 'im',
    );

    /**
     * @throws \Exception
     */

    public function run() {
        //记录发送时间.
        $start = microtime(true);
        //读取所有的在发送时间,在当前时间10分钟之内的时间
        $params = array(
            'send_at_start' => date('Y-m-d H:i:s',strtotime('-30 minute')),
            'send_at_end'   => date('Y-m-d H:i:s',strtotime('+10 minute')),
            'status'   => 1,
            'send_status'=>0
        );

        $message_list = Api::atom( 'message_send/message_list_get', $params );
        if(!empty($message_list)){
            $message_ids = ArrayUtilities::my_array_column($message_list,'msg_id');
            $update_params = array('msg_id' => $message_ids,'send_status' =>1);
            //更改数据
            $info = Api::atom( 'message_send/message_list_update', $update_params );
            if($info<=0){
                $this->app->response->setBody(date('Y-m-d H:i:s') . "更新消息队列失败\n");
            }
            foreach($message_list as $key=>$value){
                $msg_id = !empty($value['msg_id'] ) ? $value['msg_id'] : 0;
                if(empty($msg_id)) continue;
                $query_detail_params = array(
                    'all' => 1,
                    'status'=>0,
                    'msg_id' => $msg_id
                );
                $user_detail_list = Api::atom( 'message_send/message_user_list_get', $query_detail_params );


                if(empty($user_detail_list)){
                    continue;
                }
                $update_worker_detail = array(

                    'pusher_id' => 21, //消息推送
                    'custom_id' => intval($value['msg_id']),
                    'custom_version' =>0,
                    'title' => $value['title'],
                    'template_id' => $value['template_id'],
                    'channel' => isset(self::$channelMap[$value['channel']]) ? self::$channelMap[$value['channel']] : 1 ,
                    'from_id' => intval($value['op_user_id']),

                    'send_at' =>  $value['send_at'],
                    'weights' => $value['weights']

                );
                $push_array = array();

                foreach($user_detail_list as $detail_key => $detail_value){

                    $update_worker_detail['content'] = $this->_paraseContent($value,$detail_value);

                    if(empty($update_worker_detail['content'])) continue;
                    $update_worker_detail['to_id'] = !empty($detail_value['to_id']) ? $detail_value['to_id'] : 0;
                    $update_worker_detail['mail'] = !empty($detail_value['mail']) ? $detail_value['mail'] : '';
                    $update_worker_detail['phone'] = !empty($detail_value['phone']) ? $detail_value['phone'] : '';
                    $push_array[] = $update_worker_detail;
                }

                $ret = Notification::model()->pushAll($push_array);
                $new_update_params = array(
                    'msg_id' => $value['msg_id'],
                    'send_status' =>10
                );

                $info = Api::atom( 'message_send/message_list_update', $new_update_params );
                if($info<=0){
                    $this->app->response->setBody(date('Y-m-d H:i:s') . "更新消息队列失败\n");
                }


            }

        }else{
            $this->app->response->setBody(date('Y-m-d H:i:s') . "无消息需要发送\n");
        }
        $end = microtime(true);
        $total = $end - $start;
        $this->app->response->setBody(date('Y-m-d H:i:s') . "发送结束.总用时：{$total}秒\n");
    }

    /**
     * 解析发送内容
     * @param $list_info
     * @param $detail_info  //用来以后替换员工的信息
     *
     * @return array
     */
    protected function _paraseContent($list_info,$detail_info){
        if(empty($list_info) ) return '';
        switch($list_info['channel']){
            case 1:
                $content = array('mail' => array('content' => $list_info['content']));
                break;
            case 2:
                $content = array('sms' => array('content' => $list_info['content']));
                break;
            case 4:
                $content = array('im' => array('content' => $list_info['content']));
                break;
        }
        return json_encode( $content );

    }





}
