<?php

namespace Atom\Scripts\User;

use Atom\Package\Worker\SendMessage;
use Atom\Package\Account\UserInfo;
use Atom\Package\Api\Otpauth;
/**
 * 动态密钥提醒
 * Class MlsVolumeRemind
 * @package Atom\Scripts\User
 */
class MfaQrcodeRemind extends \Frame\Script{

    public function run(){

        $params = array(
            'status' => array(1,3),
        );

        $records = UserInfo::model()->getDataList($params,1,9999);

        if(!empty($records)){
            foreach($records as $r){
                //判断是否有二维码
                $p = array(
                    'user_id'   => $r['user_id'],
                    'status'    => 2,//启用
                );
                $is_otpauth = Otpauth::model()->getDataList($p);

                if(empty($is_otpauth)){
                    //通知审批人
                    $mail_params = array(
                        'token_type'    => 'process',
                        'content'       => array(
                            'username' => $r['name_cn'],
                        ),
                        'template_id'   => 227,
                        'to_id'         => $r['user_id'],
                        'title'         => "【SPEED提醒】你的SPEED动态密钥还没设置",
                        'mobile'        => '',
                        'mail'          => $r['mail'].'@meilishuo.com',
                    );
                    SendMessage::getInstance()->sendMail($mail_params);
                }
            }
        }else{
            $date = date('Y-m-d H:i:s');
            $this->response->setBody("send_time:{$date} no data!\n");
            return false;
        }

        global $start;
        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

}
