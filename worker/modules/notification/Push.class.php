<?php
namespace Worker\Modules\Notification;
use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;

/**
 * 通知推送
 *
 * Class Push
 * @package Worker\Modules\Notification
 */
class Push extends \Worker\Modules\Common\BaseModule {

    protected $pusher = array();
    protected $notify = array();


    public function run() {
        $this->init();

        $ret = Notification::model()->push($this->notify);

        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
        //参数的校验规则，无需检测是否有错误，自动输出错误信息
        $this->rules = array(
            'token'=>array(
                'required' => true,
                'type'=>'string',
                'maxLength' => 32,
            ),
            'custom_id' => array(
                'required' => true,
                'type' => 'integer',
            ),
//            'content' => array(
//                'required' => true,
//                'type' => 'string',
//            ),
            'to_id' => array(
                'required' => true,
                'type' => 'integer',
            ),
            'send_at' => array(
                'required' => true,
                'type' => 'string',
            ),
            'title' => array(
                'type' => 'string',
                'maxLength' => 255,
            ),
            'custom_version' => array(
                'type' => 'integer',
            ),
            'template_id' => array(
                'type' => 'integer',
            ),
            'channel' => array(
                'type' => 'string',
            ),
            'from_id' => array(
                'type' => 'integer',
            ),
            'mail' => array(
                'type' => 'string',
            ),
            'phone' => array(
                'type' => 'string',
            ),
        );
        $params = $this->post()->safe();
        $content = $this->post()->safe('content','array');
        if(empty($content)){
            throw new ParameterException('推送内容不能为空.');
        }

        $this->pusher = Pusher::model()->getByToken($params['token']);
        if(empty($this->pusher)){
            throw new ParameterException('token 无效');
        }

        if(empty($params['mail']) && empty($params['phone'])){
            throw new ParameterException('邮箱和手机号必须提供一个.');
        }
        $params['channel'] = explode('|',$params['channel']);
        if(count($params['channel']) == 0){
            throw new ParameterException('未指定通知方式.');
        }

        foreach($params['channel'] as $channel){
            if(!isset($content[$channel])){
                throw new ParameterException("{$channel}的推送内容不能为空.");
            }
        }

        $params['content'] = $content;
        $params['pusher_id'] = $this->pusher['pusher_id'];
        $params['weights'] = 20;
        switch($params['pusher_id']){
            case 5:
                $params['weights'] = 1;
                break;
            case 1:
                $params['weights'] = 2;
                break;
            default:
                $params['weights'] = 20;
                break;
        }
        $params['template_id'] = empty($params['template_id']) ? $this->pusher['default_template'] : $params['template_id'];
        $sendAt = strtotime($params['send_at']);
        $min = time() - 3600; //一小时以内
        if($sendAt < $min){
            throw new ParameterException("发送时间不能在一个小时以前.");
        }
        $params['send_at'] = date('Y-m-d H:i:s',$sendAt);

        $this->notify = $params;
    }
}