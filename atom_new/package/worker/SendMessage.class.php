<?php
namespace Atom\Package\Worker;

/**
 * 消息发送接口
 * @package Atom\Package\Worker
 * @author minggeng@meilishuo.com
 * @since 2015-10-30
 */

use Libs\Util\ArrayUtilities;
use Frame\Speed\Lib\Api;

class SendMessage{

    private static $instance = null;
    private $token = array(
        'meeting' => 'b4df58056d151206066eb9b8e2f52240',
        'password'=> '747e8e9223dee90fb480685ded958713',
        'user_time' => '26dd5c911e291ca574766b2138d57282',
        'process'   => '01460837ac906d0202b1a516a085f942',
        'remind'   => 'f89ca9ee98ca3acc77e75349b400b350',
    );

    private function __construct() {}

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    //IM发送
    public function sendIm($params = array()) {
        $date = date('Y-m-d H:i:s',time());
        $content['im'] = $params['content'];

        $array_datas = array(
            'token' => $this->token[$params['token_type']],
            'custom_id' => '1',
            'content' => $content,
            'to_id' => $params['to_id'],
            'send_at' => $date,
            'title' => $params['title'],
            'template_id' => $params['template_id'],
            'channel' => 'im',
            'mail' => $params['mail'],
        );
        $return  = Api::worker('notification/push',$array_datas);
        return $return;
    }

    //短信发送
    public function sendSms($params = array()) {
        $date = date('Y-m-d H:i:s',time());
        $content['sms'] = $params['content'];
        $array_datas = array(
            'token' => $this->token[$params['token_type']],
            'custom_id' => '1',
            'content' => $content,
            'to_id' => $params['to_id'],
            'send_at' => $date,
            'title' => $params['title'],
            'template_id' => $params['template_id'],
            'channel' => 'sms',
            'phone' => $params['mobile']
        );

        $return  = Api::worker('notification/push',$array_datas);

        return $return;
    }

    //邮件发送
    public function sendMail($params = array()) {
        $date = date('Y-m-d H:i:s',time());
        $content['mail'] = $params['content'];
        $array_datas = array(
            'token' => $this->token[$params['token_type']],
            'custom_id' => '1',
            'content' => $content,
            'to_id' => $params['to_id'],
            'send_at' => $date,
            'title' => $params['title'],
            'template_id' => $params['template_id'],
            'channel' => 'mail',
            'mail' => $params['mail']
        );
        $return  = Api::worker('notification/push',$array_datas);

        return $return;
    }

} 
