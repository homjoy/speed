<?php

namespace Atom\Scripts\routine;

use Atom\Package\Routine\UserTimeVersion;
use Atom\Package\Mail\MailUserTime;
class UserTimeSendMessage extends \Frame\Script {

    public $sendUrl = array(
        'delete'=> 'notification/message_delete_batch',
        'add'   => 'notification/push_all',
    );

    public function run() {
        try{
            $begin = microtime(TRUE);
            $date = date('Y-m-d H:i:s');
            //1. 获取所有未处理个人时间信息,根据time_id合并数据
            $param = array(
                'is_operate' => 0,
                'need_remind' => 1,
            );
            $res = UserTimeVersion::model()->getList($param);
            if(empty($res)){
                $this->response->setBody("send_time:{$date} no data!\n");
                return false;
            }
            $time_info_version = $this->_filterTimeInfoVersion($res);

            foreach($time_info_version as $time_id => $b_version){
                //2. add&delete
                if(array_key_exists('add',$b_version) && array_key_exists('delete',$b_version)){
                    //无操作,修改状态
                    $this->_setUserTimeStatus($b_version);
                }else if(array_key_exists('add',$b_version) && !array_key_exists('delete',$b_version)){
                    //3. add&!delete(新增操作)
                    //组织最后一个版本进行信息发送
                    $user_time = end($b_version);
                    //常规邮件提醒
                    $res = MailUserTime::sendAddMail($user_time);
                    //数据切割
                    $res = $this->_dataSpilt($res);
                    //发送消息
                    $this->_sendMessage($res);
                    //修改状态
                    $this->_setUserTimeStatus($b_version);
                }else if(!array_key_exists('add',$b_version) && array_key_exists('delete',$b_version)){
                    //4. !add&delete(删除操作)
                    $user_time = end($b_version);
                    $res = MailUserTime::sendDeleteMail($user_time);
                    //发送消息
                    $this->_sendMessage($res);
                    //删除签到
                    //修改状态
                    $this->_setUserTimeStatus($b_version);
                }else if(array_key_exists('update',$b_version)){
                    //5. update
                    $user_time = end($b_version);
                    $res = MailUserTime::sendUpdateMail($user_time);
                    //数据切割
                    $res = $this->_dataSpilt($res);
                    //发送消息
                    $this->_sendMessage($res);
                    //变更签到
                    //修改状态
                    $this->_setUserTimeStatus($b_version);
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
                    $this->app->log->log('crontab/routine/time_send_message_log', array(
                        $this->sendUrl[$key] => $param_json,
                    ));
                }else{
                    //记录日志，使用缓存记录请求次数？防止接口挂了的时候无限请求？
                    if($response['content']['code'] == 200){
                        $result = true;
                    }else if($response['content']['code'] == 400){
                        //记录日志
                        $param_json = json_encode($response);
                        $this->app->log->log('crontab/routine/time_send_message_log', array(
                            $this->sendUrl[$key] => $param_json,
                        ));
                    }
                }
            } while($retry_count > 3 && $retry);
        }
        return $result;
    }

    /**
     * 更新user_time_version发送记录状态
     * @param $b_version
     */
    private function _setUserTimeStatus($b_version){
        //修改状态
        foreach($b_version as $type => $b_v){
            unset($b_v['user_ids']);
            $b_v['is_operate'] = 1;
            if($type == 'update'){
                UserTimeVersion::model()->updateOperateById($b_v['time_id'],$b_v['version']);
            }else{
                UserTimeVersion::model()->insertOrUpdate($b_v);
            }
        }
    }

    /**
     * 根据操作类型合并数据
     * @param $data
     * @return $result array()
     */
    private function _filterTimeInfoVersion($data){
        $result = array();
        if(empty($data)){
            return $data;
        }
        foreach($data as $d){
            if(array_key_exists($d['time_id'],$result)){
                $result[$d['time_id']][$d['operate']] = $d;
            }else{
                $result[$d['time_id']] = array();
                $result[$d['time_id']][$d['operate']] = $d;
            }
            $result[$d['time_id']][$d['operate']]['user_ids'] = json_decode($d['user_id_json'],true);
        }
        return $result;
    }
}
