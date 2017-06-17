<?php
namespace Worker\Modules\Notification;

use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;
use Frame\Speed\Lib\Api;
/**
 * 通知推送
 *
 * Class OldSpeedPush
 *
 * @package Worker\Modules\Notification
 */
class OldSpeedPush extends \Worker\Modules\Common\BaseModule
{

    protected $pusher = array();
    protected $notify = array();
    protected $params = '';


    public function run()
    {
        $this->init();
        $params = json_decode($this->params, true);

        $this->notify['send_at'] = date('Y-m-d H:i:s');
        $this->notify['template_id'] = 300;//公共模板
        $this->notify['pusher_id'] = 19;
        $this->notify['custom_id'] = 0;
        $this->notify['custom_version'] = 0;
        $this->notify['channel'] = 'mail';
        $this->notify['from_id'] = 0;
        $this->notify['phone'] = '';
        $this->notify['weights'] = 20;
        $ret = array('status' => 'error');
        //内容
        if (!empty($params['mail_data']['mail_content'])) {
            $mail_data = array('mail' => array('content' => $params['mail_data']['mail_content']));
            $this->notify['content'] = $mail_data;
        } else {
            return $this->app->response->setBody($ret);
        }
        //标题
        if (!empty($params['mail_data']['mail_title'])) {
            $this->notify['title'] = $params['mail_data']['mail_title'];
        } else {
            return $this->app->response->setBody($ret);
        }
        //邮箱
        if (!empty($params['email'])) {
            $this->notify['mail'] = $params['email'];
            $query_mail = explode('@',$params['email']);
            $search_mail = $query_mail[0];
            $user_info_params = array('mail' =>$search_mail);

            $user_info = Api::atom('account/get_user_info', $user_info_params);
            if(!empty($user_info)){
                $user_info = current($user_info);
                $this->notify['to_id'] = $user_info['user_id'];
            }else{
                $this->notify['to_id'] = 100000;
            }
        } else {
            return $this->app->response->setBody($ret);
        }
        $ret = Notification::model()->push($this->notify);
        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
        //参数的校验规则，无需检测是否有错误，自动输出错误信息
        $this->params = file_get_contents("php://input");
    }
}