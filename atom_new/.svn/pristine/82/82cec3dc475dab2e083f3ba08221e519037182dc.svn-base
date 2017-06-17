<?php

namespace Atom\Scripts\User;

use Atom\Package\Worker\SendMessage;
/**
 * mls体验金提醒
 * Class MlsVolumeRemind
 * @package Atom\Scripts\User
 */
class MlsVolumeRemind extends \Frame\Script{

    private static $api = 'http://home.meilishuo.com/index.php?app=homeapi';

    public function run(){

        //查询没在home发过评论的信息
        $records = $this->mlsVolume();

        if(!empty($records)){
            foreach($records as $r){
                //通知审批人
                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => array(
                        'test' => 'test',
                    ),
                    'template_id'   => 226,
                    'to_id'         => $r['user_id'],
                    'title'         => "【没有反馈体验金提醒】",
                    'mobile'        => '',
                    'mail'          => $r['mail'],
                );
                SendMessage::getInstance()->sendMail($mail_params);
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

    /**
     * MLS体验金
     * @param string $mail 邮箱
     * @return unknown|boolean|mixed
     */
    public function mlsVolume() {
        $param = array(
            'mod'   => 'WeiBo',
            'act'   => 'getUnPostList',
        );
        $url = self::$api . '&' . http_build_query($param);
        $ret = self::getCurlObj()->get($url);

        if(!isset($ret['body']) || empty($ret['body'])) {
            return 1;
        }
        $body = json_decode($ret['body'], TRUE);
        if(json_last_error() != JSON_ERROR_NONE) {
            return 1;
        }

        return $body;
    }

    public static function getCurlObj() {
        static $curl_obj = NULL;
        if(is_null($curl_obj)) {
            $curl_obj = new \Libs\Sphinx\curl;
        }
        return $curl_obj;
    }

}
