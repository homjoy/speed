<?php
namespace Atom\Package\Mail;

use Atom\Package\Account\UserInfo;
use Atom\Package\Meeting\MeetingRoom;
use Atom\Package\Meeting\RoomService;
use Atom\Package\Meeting\RoomServiceRule;
use Atom\Package\Routine\BookHasServices;
use Atom\Package\Routine\EventReply;
use Atom\Package\Mail\MailGroupUser;
use Atom\Package\Common\SqlUtils;
use Libs\Util\ArrayUtilities;
/**
 * Class MailBook
 * @package Atom\Package\Mail
 */
class MailBook extends MailBase{
    public static $token = 'b4df58056d151206066eb9b8e2f52240';
    public static $title = '【会议提醒】主题：';
    const BOOK_BEFORE_TIME = 10;
    const BOOK_AFTER_TIME = 10;

    public static $templateMap = array(
        'common_meeting_cancel_servicer' => 101,
        'common_meeting_cancel_user' => 102,
        'common_meeting_create_servicer' => 103,
        'common_meeting_create_user' => 104,
        'common_meeting_end_servicer' => 105,
        'common_meeting_end_user' => 106,
        'common_meeting_start_servicer' => 107,
        'common_meeting_start_user' => 108,
        'common_meeting_start_initiator' => 109,
        'common_meeting_update_change_to_servicer' => 110,
        'common_meeting_update_change_to_user' => 111,
        'common_meeting_update_change_user_to_user' => 113,
        'common_meeting_update_cancel_service' => 114,

        'mail_meeting_cancel_sender' => 201,
        'mail_meeting_cancel_servicer' => 202,
        'mail_meeting_cancel_user' => 203,
        'mail_meeting_create_initiator' => 204,
        'mail_meeting_create_servicer' => 205,
        'mail_meeting_create_user' => 206,
        'mail_meeting_end_servicer' => 207,
        'mail_meeting_end_user' => 208,
        'mail_meeting_reply_decline' => 209,
        'mail_meeting_start_user' => 210,
        'mail_meeting_start_servicer' => 211,
        'mail_meeting_update_sender' => 212,
        'mail_meeting_update_servicer' => 213,
        'mail_meeting_update_change_user_to_user' => 214,
        'mail_meeting_start_initiator' => 215,
        'mail_meeting_update_cancel_service' => 216,

        'im_meeting_reply_decline' => 401,
    );

    /**
     * 会议室常规提醒
     * @param $book
     * @param $first_send（重复时，判断是否需要发送给预定人）
     * @return $result
     */
	public static function sendAddMail($book,$first_send = true){
        $result = array();
        $result_add = array();
        //受邀请人
        $users = json_decode($book['user_id_json'],true);
        $users = self::_getGroupUsers($users);
        $reserve_user = parent::getUser($book['user_id'],true);
        //var_dump($users);die();
        foreach($users as $u_id){
            //预定后就发
            $send_time = date('Y-m-d H:i:s');
            if($first_send) {
                if ($u_id == $book['user_id']) {
                    $result_add[] = self::_createParam($u_id, $book, $reserve_user, 'mail', $send_time, 'mail_meeting_create_initiator');
                } else {
                    $result_add[] = self::_createParam($u_id, $book, $reserve_user, 'mail', $send_time, 'mail_meeting_create_user');
                    //$result_add[] = self::_createParam($u_id, $book, $reserve_user, 'sms', $send_time, 'common_meeting_create_user');
                    $result_add[] = self::_createParam($u_id, $book, $reserve_user, 'im', $send_time, 'common_meeting_create_user');
                }
            }

            //会前10分钟
            $date = strtotime($book['book_start']) - self::BOOK_BEFORE_TIME*60;
            $send_time = date("Y-m-d H:i:s",$date);
            if ($u_id == $book['user_id']) {
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_start_initiator');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_start_initiator');
            }else{
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_start_user');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_start_user');
            }
            $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_start_user');

            //结束前10分钟（发起人）
            $date = strtotime($book['book_end']) - self::BOOK_AFTER_TIME*60;
            $send_time = date("Y-m-d H:i:s",$date);
            if($u_id == $book['user_id']){
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_end_user');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_end_user');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_end_user');
            }
        }
        //服务相关人员
        $notice_service = self::_getBookNoticeService($book);
        if(!empty($notice_service)){
            //遍历所有服务信息
            foreach($notice_service as $n_service){
                //每个服务人
                foreach($n_service['user'] as $u_id) {
                    //预定后就发
                    $send_time = date('Y-m-d H:i:s');
                    if($first_send) {
                        $result_add[] = self::_createParam($u_id, $book, $reserve_user, 'mail', $send_time, 'mail_meeting_create_servicer');
                        //$result_add[] = self::_createParam($u_id, $book, $reserve_user, 'sms', $send_time, 'common_meeting_create_servicer');
                        $result_add[] = self::_createParam($u_id, $book, $reserve_user, 'im', $send_time, 'common_meeting_create_servicer');
                    }
                    //会前10分钟
                    $date = strtotime($book['book_start']) - self::BOOK_BEFORE_TIME*60;
                    $send_time = date("Y-m-d H:i:s",$date);
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_start_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_start_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_start_servicer');

                    //结束前10分钟
                    /*
                    $date = strtotime($book['book_end']) - self::BOOK_AFTER_TIME*60;
                    $send_time = date("Y-m-d H:i:s",$date);
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_end_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_end_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_end_servicer');
                    */
                }
            }
        }

        $result['add'] = array(
            'token' => self::$token,
            'msg'   => $result_add,
        );

        return $result;
    }

    /**
     * 会议室删除提醒
     * @param $book
     * @return $result
     */
    public static function sendDeleteMail($book){
        $result = array();
        $result_add = array();
        $result_delete = array();
        //删除消息发送队列未发送信息
        $result_delete[] = self::_createDelParam($book['book_id']);

        //受邀请人
        $users = json_decode($book['user_id_json'],true);
        $users = self::_getGroupUsers($users);
        $reserve_user = parent::getUser($book['user_id'],true);
        foreach($users as $u_id){
            //通知所有人会议取消，预定后就发
            $send_time = date('Y-m-d H:i:s');
            if($u_id == $book['user_id']){
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_cancel_sender');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_cancel_user');
            }else{
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_cancel_user');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_cancel_user');
                $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_cancel_user');
            }
        }

        //服务相关人员
        $notice_service = self::_getBookNoticeService($book);

        if(!empty($notice_service)){
            //遍历所有服务信息
            foreach($notice_service as $n_service){
                //每个服务人
                foreach($n_service['user'] as $u_id) {
                    //预定后就发
                    $send_time = date('Y-m-d H:i:s');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'mail',$send_time,'mail_meeting_cancel_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'sms',$send_time,'common_meeting_cancel_servicer');
                    $result_add[] = self::_createParam($u_id,$book,$reserve_user,'im',$send_time,'common_meeting_cancel_servicer');
                }
            }
        }
        //要先删除后新增
        $result['delete'] = array(
            'token' => self::$token,
            'msg'   => $result_delete,
        );
        $result['add'] = array(
            'token' => self::$token,
            'msg'   => $result_add,
        );
        return $result;
    }

    /**
     * 会议室变更提醒
     * @param $book_after
     * @param $diffBook
     * @return $result
     */
    public static function sendUpdateMail($book_after,$diffBook){
        $result = array();
        $result_add = array();
        $result_delete = array();
        $reserve_user = parent::getUser($book_after['user_id'],true);
        $diff_key = $diffBook[0];
        $diff_info = $diffBook[1];
        if(isset($diff_key['meeting_time'])||isset($diff_key['meeting_room'])){
            //删除该会议之前所有未发送消息，重新推送
            $result_delete[] = self::_createDelParam($book_after['book_id']);

            //针对所有参会人员
            $users = json_decode($book_after['user_id_json'],true);
            $users = self::_getGroupUsers($users);
            foreach($users as $u_id){
                //预定后就发
                $send_time = date('Y-m-d H:i:s');
                if($u_id == $book_after['user_id']){
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_update_sender',$diff_info);
                }else{
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_update_sender',$diff_info);
                    //$result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_update_change_to_user',$diff_info);
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_update_change_to_user',$diff_info);
                }

                //会前10分钟
                $date = strtotime($book_after['book_start']) - self::BOOK_BEFORE_TIME*60;
                $send_time = date("Y-m-d H:i:s",$date);
                if ($u_id == $book_after['user_id']) {
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_start_initiator',$diff_info);
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_start_initiator',$diff_info);
                }else{
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_start_user',$diff_info);
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_start_user',$diff_info);
                }
                $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_start_user',$diff_info);

                //结束前10分钟（发起人）
                $date = strtotime($book_after['book_end']) - self::BOOK_AFTER_TIME*60;
                $send_time = date("Y-m-d H:i:s",$date);
                if($u_id == $book_after['user_id']) {
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_end_user',$diff_info);
                    $result_add[] = self::_createParam($u_id, $book_after, $reserve_user, 'sms', $send_time, 'common_meeting_end_user', $diff_info);
                    $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_end_user',$diff_info);
                }
            }
            //所有服务人员
            $notice_service = self::_getBookNoticeService($book_after);
            $reserve_user = parent::getUser($book_after['user_id'],true);
            if(!empty($notice_service)){
                //遍历所有服务信息
                foreach($notice_service as $n_service){
                    //每个服务人
                    foreach($n_service['user'] as $u_id) {
                        //预定后就发
                        $send_time = date('Y-m-d H:i:s');
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_update_servicer',$diff_info);
                        //$result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_update_change_to_servicer',$diff_info);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_update_change_to_servicer',$diff_info);

                        //会前10分钟
                        $date = strtotime($book_after['book_start']) - self::BOOK_BEFORE_TIME*60;
                        $send_time = date("Y-m-d H:i:s",$date);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_start_servicer',$diff_info);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_start_servicer',$diff_info);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_start_servicer',$diff_info);

                        /*
                        //结束前10分钟
                        $date = strtotime($book_after['book_end']) - self::BOOK_AFTER_TIME*60;
                        $send_time = date("Y-m-d H:i:s",$date);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_end_servicer',$diff_info);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_end_servicer',$diff_info);
                        $result_add[] = self::_createParam($u_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_end_servicer',$diff_info);
                        */
                    }
                }
            }
        }else{
            if(isset($diff_key['user_add'])){
                $users = self::_getGroupUsers($diff_key['user_add']);
                foreach($users as $user){
                    //预定后就发
                    $send_time = date('Y-m-d H:i:s');
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_create_user',$diff_info);
                    //$result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_create_user',$diff_info);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_create_user',$diff_info);

                    //会前10分钟
                    $date = strtotime($book_after['book_start']) - self::BOOK_BEFORE_TIME*60;
                    $send_time = date("Y-m-d H:i:s",$date);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_start_user',$diff_info);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_start_user',$diff_info);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_start_user',$diff_info);

                    //结束前10分钟
                    $date = strtotime($book_after['book_end']) - self::BOOK_AFTER_TIME*60;
                    $send_time = date("Y-m-d H:i:s",$date);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_end_user',$diff_info);
                    //$result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_end_user',$diff_info);
                    $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_end_user',$diff_info);
                }
            }
            if(isset($diff_key['service_add'])){
                foreach($diff_key['service_add'] as $a_service){
                    //根据服务获取发送相关服务人员
                    $service_users = self::_getServiceUsers($book_after['room_id'],$a_service);
                    foreach($service_users as $user) {
                        //预定后就发
                        $send_time = date('Y-m-d H:i:s');
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_create_servicer',$diff_info);
                        //$result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_create_servicer',$diff_info);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_create_servicer',$diff_info);

                        //会前10分钟
                        $date = strtotime($book_after['book_start']) - self::BOOK_BEFORE_TIME*60;
                        $send_time = date("Y-m-d H:i:s",$date);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_start_servicer',$diff_info);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_start_servicer',$diff_info);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_start_servicer',$diff_info);

                        //结束前10分钟
                        $date = strtotime($book_after['book_end']) - self::BOOK_AFTER_TIME*60;
                        $send_time = date("Y-m-d H:i:s",$date);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_end_servicer',$diff_info);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'sms',$send_time,'common_meeting_end_servicer',$diff_info);
                        $result_add[] = self::_createParam($user,$book_after,$reserve_user,'im',$send_time,'common_meeting_end_servicer',$diff_info);
                    }
                }
            }
        }
        if(isset($diff_key['service_del'])){
            foreach($diff_key['service_del'] as $d_service){
                //根据服务获取发送相关服务人员
                $service_users = self::_getServiceUsers($book_after['room_id'],$d_service);
                foreach($service_users as $user_id) {
                    //删除会议服务人员未处理消息
                    $result_delete[] = self::_createDelParam($book_after['book_id'],$user_id);
                    //预定后就发
                    $send_time = date('Y-m-d H:i:s');
                    $result_add[] = self::_createParam($user_id,$book_after,$reserve_user,'mail',$send_time,'mail_meeting_update_cancel_service',$diff_info);
                    $result_add[] = self::_createParam($user_id,$book_after,$reserve_user,'sms',$send_time,'common_meeting_update_cancel_service',$diff_info);
                    $result_add[] = self::_createParam($user_id,$book_after,$reserve_user,'im',$send_time,'common_meeting_update_cancel_service',$diff_info);
                }
            }
        }
        if(isset($diff_key['user_del'])){
            //拒绝参加会议人员信息
            $decline_users = EventReply::model()->getDeclineUsers($book_after['book_id']);
            $users = self::_getGroupUsers($diff_key['user_del']);
            foreach($users as $user_id){
                //删除会议参与人未处理消息
                $result_delete[] = self::_createDelParam($book_after['book_id'],$user_id);
                //预定后就发
                $send_time = date('Y-m-d H:i:s');
                if(in_array($user_id,$decline_users)) {
                    //拒绝时通知发起人
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'mail', $send_time,'mail_meeting_reply_decline',$diff_info);
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'sms', $send_time,'im_meeting_reply_decline',$diff_info);
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'im', $send_time,'im_meeting_reply_decline',$diff_info);
                }else{
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'mail', $send_time,'mail_meeting_update_change_user_to_user',$diff_info);
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'sms', $send_time,'common_meeting_update_change_user_to_user',$diff_info);
                    $result_add[] = self::_createParam($user_id, $book_after, $reserve_user, 'im', $send_time,'common_meeting_update_change_user_to_user',$diff_info);
                }
            }
        }
        //一定要先删除后推送
        $result['delete'] = array(
            'token' => self::$token,
            'msg'   => $result_delete,
        );
        $result['add'] = array(
            'token' => self::$token,
            'msg'   => $result_add,
        );
        return $result;
    }

    /**
     * 获取会议室所有服务&人员信息
     * @param $room_id
     * @param $service_id
     * @return $result
     */
    private static function _getServiceUsers($room_id,$service_id){
        $service_rule = RoomServiceRule::model()->getRule($room_id,$service_id,'1');
        $service_rule = array_pop($service_rule);
        if(empty($service_rule['config'])){
            $room_service = RoomService::model()->getById($service_id);
            $service_config = json_decode($room_service['config'],true);
            return $service_config['user'];
        }else{
            $service_config = json_decode($service_rule['config'],true);
            return $service_config['user'];
        }
    }

    /**
     * 删除会议室请求参数
     * @param $book_id
     * @param $to_user_id(如果为空时，删除全部信息)
     * @return $result
     */
    private static function _createDelParam($book_id,$to_user_id = ''){
        $push_param = array();
        $push_param['custom_id'] = $book_id;
        if($to_user_id){
            $push_param['to_user_id'] = $to_user_id;
        }
        return $push_param;
    }

    /**
     * 根据邮件组获取所有参会人
     * @param $users
     * @return $result
     */
    private static function _getGroupUsers($users){
        $result = array();
        if(empty($users)){
            return array();
        }
        foreach($users as $u){
            //邮件组
            if($u > 100000){
                $queryParams = array(
                    'group_id' => $u,
                );
                $group_user = MailGroupUser::getInstance()->getDataList($queryParams, 1, 1000);
                $groupInfoMap = array();
                if(!empty($group_user)){
                    $groupInfoMap = ArrayUtilities::my_array_column($group_user,'user_id');
                    $groupInfoMap = SqlUtils::formatInt($groupInfoMap);
                    //过滤离职人员
                    foreach($groupInfoMap as $k => $g){
                        $user = parent::getUser($g);
                        if(empty($user)){
                            unset($groupInfoMap[$k]);
                        }
                    }
                }
                $result = array_merge($result,$groupInfoMap);
            }else{
                $user = parent::getUser($u);
                //过滤离职人员
                if($user){
                    $result[] = $u;
                }
            }
        }
        $result = array_unique($result);
        return $result;
    }

    /**
     * 创建请求发送消息接口参数
     * @param $user_id
     * @param $book
     * @param $reserve_user
     * @param $send_type
     * @param $send_time
     * @param $template_key
     * @param $diff_info
     * @return $result
     */
    private static function _createParam($user_id,$book,$reserve_user,$send_type,$send_time,$template_key,$diff_info=array()){
        $book_param = array();
        $book_param['book_id'] = $book['book_id'];
        $book_param['initiator'] = $reserve_user['name_cn'];
        $book_param['meeting_topic'] = $book['meeting_topic'];
        $book_param['meeting_type'] = parent::getMeetingType($book['meeting_type']);
        //重复会议室，如果第一次使用book_start
        if($book['book_date'] <= date('Y-m-d')){
            $book_param['meeting_time'] = parent::getTime(date('Y-m-d'),$book['time_start'],$book['time_end'],date('w'));
            $book_param['meeting_week_day'] = parent::getWeek(date('w'));
        }else{
            $book_param['meeting_time'] = parent::getTime($book['book_date'],$book['time_start'],$book['time_end'],$book['week_day']);
            $book_param['meeting_week_day'] = parent::getWeek($book['week_day']);
        }
        $book_param['meeting_start_time'] = parent::getTime(date('Y-m-d'),$book['time_start'],$book['time_end'],$book['week_day'],true);
        $user = parent::getUser($user_id);
        if(!empty($user)){
            $book_param['username'] = $user['name_cn'];
        }
        $book_param['changed_fields'] = $diff_info;
        $book_param['meeting_service'] = $book['meeting_service'];
        $r_u = array();
        //循环每个分会场人员信息

        foreach ($book['user_ids'] as $room_id => $user_ids) {
            $meeting = MeetingRoom::getInstance()->getDataById($room_id);
            //处理邮件组信息
            $users = self::_getGroupUsers($user_ids);
            if(empty($users)){
                $invite_str = '';
            }else{
                $u_ids = UserInfo::model()->getDataList(array('user_id'=>$users),0,999);
                $invite_str = array();
                if (!empty($u_ids) && is_array($u_ids)) {
                    foreach ($u_ids as $u) {
                        $invite_str[] = $u['name_cn'];
                    }
                    $invite_str = join('，',$invite_str);
                }
            }

            if($room_id == $book['room_id']){
                if(!empty($meeting['room_position'])){
                    $book_param['place'] = $meeting['room_name'].'（'.$meeting['room_position'].'）';
                }else{
                    $book_param['place'] = $meeting['room_name'];
                }
            }
            $r_u['place'] = $meeting['room_name'];
            $r_u['room_position'] = $meeting['room_position'];
            $r_u['invite_users'] = $invite_str;
            $book_param['zones'][$room_id] = $r_u;
        }
        //发送信息
        $push_param = array();
        $push_param['custom_id'] = $book['book_id'];
        $push_param['custom_version'] = '';
        $push_param['title'] = self::$title.$book_param['meeting_topic'];
        $push_param['content'] = array($send_type => $book_param);
        $push_param['from_id'] = 0;
        $template_id = self::$templateMap[$template_key];
        $push_param['template_id'] = $template_id;
        $push_param['channel'] = $send_type;
        $push_param['send_at'] = $send_time;
        if(!empty($user)){
            $push_param['to_id'] = $user['user_id'];
            $push_param['mail'] = $user['mail'] . '@meilishuo.com';
            $push_param['phone'] = $user['mobile'];
        }

        return $push_param;
    }

    /**
     * 获取会议室全部服务信息
     * @param $book
     * @return $result
     */
    private static function _getBookNoticeService($book){
        //预定需要通知的服务
        $noticeServices = array();
        //查询预定用到的服务
        $hasServices = BookHasServices::model()->getBookServices($book['book_id']);
        //没有需要通知的
		if(empty($hasServices)) {
			return array();
		}
		//提取服务ID
		$serviceIds = ArrayUtilities::my_array_column($hasServices,'service_id');

		//查询服务以及配置
		$roomService = RoomService::model()->getServiceList(array(
			'service_id'=>$serviceIds,
			'status' => 1,
		));

		//查询房间的配置规则
		$roomServiceRule = RoomServiceRule::model()->getRule($book['room_id'],$serviceIds,1);
		$roomServiceRule = ArrayUtilities::hashByKey($roomServiceRule,'service_id');

		//生成该服务的通知配置
		foreach($hasServices as $hasService) {
			$serviceId = $hasService['service_id'];
			//如果针对房间专门制定了通知规则
			if(!empty($roomServiceRule[$serviceId]['config'])){
				$config = $roomServiceRule[$serviceId]['config'];
			}else{
				//使用服务的默认配置
				$config = $roomService[$serviceId]['config'];
			}

			//如果都没有配置，那就是不需要通知
			//否则将JSON格式的配置解析出来
			$noticeServices[$serviceId] = empty($config) ? null : json_decode($config,true);
		}

		//预定的通知列表
		return $noticeServices;
    }

}
