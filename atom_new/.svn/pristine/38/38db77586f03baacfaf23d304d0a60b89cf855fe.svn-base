<?php

namespace Atom\Scripts;

use Atom\Package\Core\MlsAccount;
use Atom\Package\User\UserInfo;
use Libs\Util\ArrayUtilities;
use Frame\Speed\Lib\Api;

/*
 * speed密码过期提醒
 * @author haibinzhou
 * @date 20-07-28
 * 每天跑一次
 *
 */

class PasswordOutWarn extends \Frame\Script {

    /**
     * 帐号类型
     */
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记

    private static $max_day = 90; //最少90天修改一次密码
    private static $advance = 7; //提前7天

    private $want = '';
    private $what = '';
    private $type = 'int';

    private $in = NULL;

    public function run() {

        $user_info = MlsAccount::model()->getByType(self::$TYPE_MAIL_PWD,1);  //根据邮箱类型获取用户注册信息

        $send_data = array();
        $user_ids = array();
        foreach($user_info as $info){
            $message = self::filter_date($info['update_time']);
            if($message === FALSE) {
                continue;
            }

            $send_data[$info['user_id']]['message'] = $message;
            $user_ids[] = $info['user_id'];
        }

        //给确定的人发邮件
        $this->want = $user_ids;
        $this->what = 'user_id';
        $this->type = 'int';
        $user_info = UserInfo::getInstance()->getUseIn($this->want,$this->what,$this->type,array(),$this->in);

        foreach($user_info as $key=>$send){  //循环发邮件
            $send_info = array();
            if(isset($send_data[$send['user_id']]['message'])){
                $send_info['message'] = $send_data[$send['user_id']]['message'];
                $send_info['name'] = $send['name_cn'];
                $send_info['user_id'] = $send['user_id'];
                $send_info['mail'] = $send['mail'].'@meilishuo.com';
            }

            $this->sendMail($send_info);
        }

        $this->response->setBody("send success");
    }

    //发邮件
    private  function sendMail($params = array()) {
        $date = date('Y-m-d H:i:s',time());

        $content['mail'] = array('name'=>$params['name'],'message'=>$params['message']);

        $array_datas = array(
            'token' => '747e8e9223dee90fb480685ded958713',
            'custom_id' => '1',
            'content' => $content,
            'to_id' => $params['user_id'], 'send_at' => $date, 'title' => '邮箱密码过期提醒', 'template_id' => 217, 'channel' => 'mail', 'mail' => $params['mail']);

        $return  = Api::worker('notification/push',$array_datas);
        //记录日志

        $msg = json_encode($array_datas);
        $msg=$msg.'return:'.json_encode($return);
         $this->app->log->log('crontab/mail_password_out_warn_log', $msg);

        return $return;
    }

    //判断密码是否过期
    private function filter_date($date) {
        $length = (time() - strtotime($date)) / 86400;  //$length 单位为天
        $left = self::$max_day - $length;
        $left = ceil(floor($left));
        if($left <= 0) {
            return "已过期";
        }else if($left <= self::$advance){
            return "{$left}天后过期";
        }else {
            return FALSE;  //未过期
        }
    }
}
