<?php

namespace Atom\Scripts\routine;

use Atom\Package\Routine\BookInfoVersion;
use Atom\Package\Routine\BookInfo;
use Atom\Package\Routine\BookHasUsers;
use Atom\Package\Mail\MailBook;
use Frame\Speed\Lib\Api;
use Atom\Package\Meeting\RoomService;
class BookSendMessage extends \Frame\Script {

    public $sendUrl = array(
        'delete'=> 'notification/message_delete_batch',
        'add'   => 'notification/push_all',
    );

    public function run() {
        try{
            $begin = microtime(TRUE);
            $date = date('Y-m-d H:i:s');
            //1. 获取所有未处理会议室信息,根据book_id合并数据
            $param = array(
                'is_operate' => 0,
            );
            $res = BookInfoVersion::model()->getList($param);
            if(empty($res)){
                $this->response->setBody("send_time:{$date} no data!\n");
                return false;
            }
            $book_info_version = $this->_filterBookInfoVersion($res);

            foreach($book_info_version as $book_id => $b_version){
                //2. add&delete
                if(array_key_exists('add',$b_version) && array_key_exists('delete',$b_version)){
                    //无操作,修改状态
                    $this->_setBookInfoStatus($b_version);
                }else if(array_key_exists('add',$b_version) && !array_key_exists('delete',$b_version)){
                    //3. add&!delete(新增操作)
                    //组织最后一个版本进行信息发送
                    $book_info = end($b_version);

                    $book_info_all = $this->_getMeetingAllUser($book_info);
                    //常规邮件提醒
                    $res = MailBook::sendAddMail($book_info_all);
                    //数据切割
                    $res = $this->_dataSpilt($res);
                    //发送消息
                    $this->_sendMessage($res);
                    //新增签到

                    //修改状态
                    $this->_setBookInfoStatus($b_version);
                }else if(!array_key_exists('add',$b_version) && array_key_exists('delete',$b_version)){
                    //4. !add&delete(删除操作)
                    //删除时，获取所有已处理会议室版本，并取最后一个
                    $res = $this->_getLastVersionByBookId($b_version['delete']['book_id']);
                    $book_info = end($res);
                    $book_info_all = $this->_getMeetingAllUser($book_info);
                    $res = MailBook::sendDeleteMail($book_info_all);
                    //数据切割
                    $res = $this->_dataSpilt($res);
                    //发送消息
                    $this->_sendMessage($res);
                    //删除签到
                    //修改状态
                    $this->_setBookInfoStatus($b_version);
                }else if(array_key_exists('update',$b_version)){
                    //5. update
                    //修改时，获取所有已处理会议室版本，并取最后一个
                    $res = $this->_getLastVersionByBookId($b_version['update']['book_id']);

                    $book_before = end($res);
                    $book_after = end($b_version);
                    //与最初版本进行diff
                    $diffBook = $this->_diffBook($book_before,$book_after);

                    $book_info_all = $this->_getMeetingAllUser($book_after);
                    $res = MailBook::sendUpdateMail($book_info_all,$diffBook);
                    //数据切割
                    $res = $this->_dataSpilt($res);
                    //发送消息
                    $this->_sendMessage($res);
                    //变更签到
                    //修改状态
                    $this->_setBookInfoStatus($b_version);
                }
            }
            $end = microtime(TRUE);
            $time=$end-$begin;
            $this->response->setBody("send_time:{$date} 执行了:{$time}s\n");
        }catch (\Exception $e){
            $this->response->setBody($e->getMessage());
        }
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
     * 根据book_id获取最后一个版本信息
     * @param $book_id
     * @return $res array()
     */
    private function _getLastVersionByBookId($book_id){
        $param = array(
            'is_operate' => 1,
            'book_id'   =>$book_id,
        );
        $res = BookInfoVersion::model()->getList($param);
        if(empty($res)){
            $param = array(
                'book_id'   =>$book_id,
            );
            $res = BookInfoVersion::model()->getList($param);
        }
        return $res;
    }

    /**
     * version_diff
     * @param $book_before,$book_after
     * @return $result array()
     */
    private function _diffBook($book_before,$book_after){
        $result = array();
        $changed_fields = array();
        //时间修改
        if($book_before['book_start']!=$book_after['book_start']||$book_before['book_end']!=$book_after['book_end']){
            $result['meeting_time'] = true;
            $changed_fields[] = 'meeting_time';
        }
        //会议类型修改
        if($book_before['meeting_type']!=$book_after['meeting_type']){
            $result['meeting_type'] = true;
            $changed_fields[] = 'meeting_type';
        }
        //会议室修改
        if($book_before['room_id']!=$book_after['room_id']){
            $result['meeting_room'] = true;
            $changed_fields[] = 'zone_'.$book_after['room_id'].'_place';
        }
        //增加参会人
        $user_before = json_decode($book_before['user_id_json'],true);
        $user_after = json_decode($book_after['user_id_json'],true);
        $add_user = array();
        foreach($user_after as $u_a){
            if(!in_array($u_a,$user_before)){
                $add_user[] = $u_a;
            }
        }
        if(!empty($add_user)){
            $result['user_add'] = $add_user;
            $changed_fields[] = 'zone_'.$book_after['room_id'].'_users';
        }
        //增加服务
        $service_before = json_decode($book_before['service_id_json'],true);
        $service_after = json_decode($book_after['service_id_json'],true);
        $add_service = array();
        foreach($service_after as $s_a){
            if(!in_array($s_a,$service_before)){
                $add_service[] = $s_a;
            }
        }
        if(!empty($add_service)){
            $result['service_add'] = $add_service;
            $changed_fields[] = 'meeting_service';
        }
        //减少服务
        $del_service = array();
        foreach($service_before as $s_f){
            if(!in_array($s_f,$service_after)){
                $del_service[] = $s_f;
            }
        }
        if(!empty($del_service)){
            $result['service_del'] = $del_service;
            $changed_fields[] = 'meeting_service';
        }
        //减少参会人
        $del_user = array();
        foreach($user_before as $u_f){
            if(!in_array($u_f,$user_after)){
                $del_user[] = $u_f;
            }
        }
        if(!empty($del_user)){
            $result['user_del'] = $del_user;
            $changed_fields[] = 'zone_'.$book_after['room_id'].'_users';
        }
        $res = array();
        $res[] = $result;
        $res[] = $changed_fields;
        return $res;
    }

    /**
     * 更新book_info_version发送记录状态
     * @param $b_version
     */
    private function _setBookInfoStatus($b_version){
        //修改状态
        foreach($b_version as $type => $b_v){
            $b_v['is_operate'] = 1;
            if($type = 'update'){
                BookInfoVersion::model()->updateOperateById($b_v['book_id'],$b_v['version']);
            }else{
                BookInfoVersion::model()->insertOrUpdate($b_v);
            }
        }
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
        $serviceIds = json_decode($book_info['service_id_json'],true);
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
        //处理重复会议室时间
        if($book_info['book_date'] <= date('Y-m-d')){
            $book_param['book_start'] = date('Y-m-d') . ' ' . $book_info['time_start'];
            $book_param['book_end'] = date('Y-m-d') . ' ' . $book_info['time_end'];
        }
        $book_info['meeting_service'] = $meeting_service;
        return $book_info;
    }

    /**
     * 根据操作类型合并数据
     * @param $data
     * @return $result array()
     */
    private function _filterBookInfoVersion($data){
        $result = array();
        if(empty($data)){
            return $data;
        }
        foreach($data as $d){
            if(array_key_exists($d['book_id'],$result)){
                $result[$d['book_id']][$d['operate']] = $d;
            }else{
                $result[$d['book_id']] = array();
                $result[$d['book_id']][$d['operate']] = $d;
            }
        }
        return $result;
    }

}
