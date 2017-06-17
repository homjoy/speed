<?php

namespace Atom\Scripts\routine;

use Atom\Package\Meeting\RoomService;
use Atom\Package\Routine\BookHasServices;
use Atom\Package\Routine\BookInfo;
use Atom\Package\Routine\BookRepeatSend;
use Atom\Package\Routine\BookHasUsers;
use Atom\Package\Mail\MailBook;
use Frame\Speed\Lib\Api;
use Libs\Util\ArrayUtilities;

class BookRepeatSendMessage extends \Frame\Script {

    public $sendUrl = array(
        'delete'=> 'notification/message_delete_batch',
        'add'   => 'notification/push_all',
    );

    public function run() {
        $begin = microtime(TRUE);
        $date = date('Y-m-d H:i:s');
        $repeat_book = BookInfo::model()->getRepeatByDay($date);
        if(empty($repeat_book)){
            $this->response->setBody("send_time:{$date} no data!\n");
            return false;
        }

        foreach($repeat_book as $book_id => $r_book){
            //1.判断是否是重复发送，如果是首次发送，直接跳过
            //判断是不是需要第一次发送给预订人
            $send_time = date('Y-m-d');
            $first_send = $send_time == $r_book['book_date'] ? true : false;
            if($first_send){
                continue;
            }
            //2.判断当天是否已经发送
            $is_send = $this->_isSendByBook($book_id,$send_time);

            if(!$is_send){
                $book_info_all = $this->_getMeetingAllUser($r_book);
                //常规邮件提醒
                $res = MailBook::sendAddMail($book_info_all,false);
                //数据切割
                $res = $this->_dataSpilt($res);
                //发送消息
                $this->_sendMessage($res);
                //新增签到
                //记录RepeatSend
                $this->_saveRepeatSend($book_id);
            }
        }
        $end = microtime(TRUE);
        $time=$end-$begin;
        $this->response->setBody("send_time:{$date} 执行了:{$time}s\n");
    }

    /**
     * 数据切割（10个一组）
     * @param $data
     * @return $data
     */
    private function _dataSpilt($data){
        $result = array();
        if(!is_array($data) && empty($data)){
            return $result;
        }else{
            $index = 1;
            foreach($data as $key => $value){
                $msg = $value['msg'];
                $data_spilt = array_chunk($msg,10);
                foreach($data_spilt as $d){
                    $result[$key.'_'.$index] = array(
                        'token' => $value['token'],
                        'msg'   => $d,
                    );
                    $index++;
                }
            }
            return $result;
        }
    }

    /**
     * 消息发送(如果失败重试3次)
     * @param $data
     * @return $result boolean
     */
    private function _sendMessage($data){
        $result = false;
        \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 0, 'ip' => '127.0.0.1'));
        $client = new \Libs\Serviceclient\Client();
        foreach($data as $k => $value){
            do{
                $retry_count = 0;
                $retry = false;

                //key=add_1,add_2
                $a_key = explode('_',$k);
                $key = $a_key[0];
                $response = $client->call('worker', $this->sendUrl[$key] , $value);
                $response['id'] = $value['msg'][0]['custom_id'];
                if($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])){
                    //重试
                    $retry_count++;
                    $retry = true;
                    //记录日志
                    $param_json = json_encode($response);
                    $this->app->log->log('book_send_message_log', array(
                        $this->sendUrl[$key] => $param_json,
                    ));
                }else{
                    //记录日志，使用缓存记录请求次数？防止接口挂了的时候无限请求？
                    if($response['content']['code'] == 200){
                        $result = true;
                    }else if($response['content']['code'] == 400){
                        //记录日志
                        $param_json = json_encode($response);
                        $this->app->log->log('book_send_message_log', array(
                            $this->sendUrl[$key] => $param_json,
                        ));
                    }
                }
            } while($retry_count > 3 && $retry);
        }
        return $result;
    }

    /**
     * 判断当天是否已经发送
     * @param $book_id
     * @param $send_time
     * @return boolean
     */
    private function _isSendByBook($book_id,$send_time){
        $param = array();
        $param['book_id'] = $book_id;
        $param['send_time'] = $send_time;
        $res = BookRepeatSend::model()->getList($param);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 记录重复会议室发送记录
     * @param $book_id
     */
    private function _saveRepeatSend($book_id){
        $params = array();
        $params['book_id'] = $book_id;
        $params['send_time'] = date('Y-m-d h:i:s');
        BookRepeatSend::model()->insertOrUpdate($params);
    }

    /**
     * 获取会议所有参会人
     * @param $book_info
     * @return $book_info array()
     */
    private function _getMeetingAllUser($book_info){
        //主会场
        if($book_info['main_book_id'] == 0){
            $book_chapter = BookInfo::model()->getById($book_info['book_id']);
        }else{
            $book_chapter = BookInfo::model()->getById($book_info['main_book_id']);
        }
        $user_ids = array();
        foreach($book_chapter as $b_chapter){
            $res = BookHasUsers::model()->getBookUserIds($b_chapter['book_id']);
            $user_ids[$b_chapter['room_id']] = array_pop($res);
        }
        $book_info['user_ids'] = $user_ids;
        $meeting_service = array();
        $res = BookHasServices::model()->getBookServices($book_info['book_id']);
        $serviceIds = ArrayUtilities::my_array_column($res,'service_id');
        if(!empty($serviceIds)){
            //查询服务以及配置
            $roomService = RoomService::model()->getServiceList(array(
                'service_id'=>$serviceIds,
                'status' => 1,
            ));
            foreach($roomService as $rs){
                $meeting_service[] = $rs['name'];
            }
            $meeting_service = join(' / ',$meeting_service);
        }
        $res = BookHasUsers::model()->getBookUserIds($book_info['book_id'],true);
        $book_info['user_id_json'] = json_encode($res[$book_info['book_id']]);
        //处理重复会议室时间
        if($book_info['book_date'] <= date('Y-m-d')){
            $book_info['book_start'] = date('Y-m-d') . ' ' . $book_info['time_start'];
            $book_info['book_end'] = date('Y-m-d') . ' ' . $book_info['time_end'];
        }
        $book_info['meeting_service'] = $meeting_service;
        return $book_info;
    }

}
