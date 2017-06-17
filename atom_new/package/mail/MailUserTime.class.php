<?php
namespace Atom\Package\Mail;

/**
 * Class MailUserTime
 * @package Atom\Package\Mail
 */
class MailUserTime extends MailBase{
    public static $token = '26dd5c911e291ca574766b2138d57282';
    public static $title = '【美丽说】个人时间提醒';

    public static $templateMap = array(
        'common_user_time_start_user' => 117,
    );

    /**
     * 个人时间常规提醒
     * @param $user_time
     * @return $result
     */
	public static function sendAddMail($user_time){
        $result = array();
        $result_add = array();
        //受邀请人
        $users = json_decode($user_time['user_id_json'],true);
        foreach($users as $u_id){
            $result_add[] = self::_createParam($u_id, $user_time, 'common_user_time_start_user');
        }

        $result['add'] = array(
            'token' => self::$token,
            'msg'   => $result_add,
        );

        return $result;
    }

    /**
     * 个人时间删除提醒
     * @param $user_time
     * @return $result
     */
    public static function sendDeleteMail($user_time){
        $result = array();
        $result_delete = array();
        //删除消息发送队列未发送信息
        $result_delete[] = self::_createDelParam($user_time['time_id']);

        //要先删除后新增
        $result['delete'] = array(
            'token' => self::$token,
            'msg'   => $result_delete,
        );
        return $result;
    }

    /**
     * 个人时间变更提醒
     * @param $user_time
     * @return $result
     */
    public static function sendUpdateMail($user_time){
        $result = array();
        $result_add = array();
        $result_delete = array();
        //删除消息发送队列未发送信息
        $result_delete[] = self::_createDelParam($user_time['time_id']);

        //受邀请人
        $users = json_decode($user_time['user_id_json'],true);
        foreach($users as $u_id){
            $result_add[] = self::_createParam($u_id, $user_time, 'common_user_time_start_user');
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
     * 创建删除个人时间请求参数
     * @param $time_id
     * @return $result
     */
    private static function _createDelParam($time_id){
        $push_param = array();
        $push_param['custom_id'] = $time_id;
        return $push_param;
    }

    /**
     * 创建请求发送消息接口参数
     * @param $user_id
     * @param $user_time
     * @param $template_key
     * @return $result
     */
    private static function _createParam($user_id,$user_time,$template_key){
        //发送时间
        $s_time = strtotime($user_time['start_time'])-$user_time['remind_time']*60;
        $send_time = date('Y-m-d H:i:s',$s_time);

        $user_time_param = array();
        $user_time_param['title'] = $user_time['title'];

        $user = parent::getUser($user_id);
        if(!empty($user)){
            $user_time_param['username'] = $user['name_cn'];
        }

        //发送信息
        $push_param = array();
        $push_param['custom_id'] = $user_time['time_id'];
        $push_param['custom_version'] = '';
        $push_param['title'] = self::$title;
        $push_param['content'] = array($user_time['remind_type'] => $user_time_param);
        $push_param['from_id'] = 0;
        $template_id = self::$templateMap[$template_key];
        $push_param['template_id'] = $template_id;
        $push_param['channel'] = $user_time['remind_type'];
        $push_param['send_at'] = $send_time;
        $push_param['to_id'] = $user_id;
        if(!empty($user)){
            $push_param['phone'] = $user['mobile'];
            $push_param['mail'] = $user['mail'].'@meilishuo.com';
        }
        return $push_param;
    }

}
