<?php
namespace Worker\Package\Notification;

/**
 * 通知请求构造工厂
 * Class RequestFactory
 * @package Worker\Package\Notification
 */
class RequestFactory
{

    protected static $api = array();

    private function __construct()
    {
    }

    /**
     * 生成请求
     * @param $notify
     * @return array
     * @throws \Exception
     */
    public static function build($notify)
    {
        if (empty($notify)) {
            throw new \Exception("通知内容为空!");
        }
        $requests = array();
        foreach ($notify['channel'] as $channel) {
            //不处理邮件的
            if($channel == 'mail'){
                continue;
            }
            $req = call_user_func('static::' . $channel, $notify);
            if ($req instanceof Request) {
                $requests[] = $req;
            }
        }
        return $requests;
    }

    /**
     * 使用本地文件发送.
     * @param $notify
     * @return array
     * @throws \Exception
     */
    public static function buildLocalMail($notify)
    {
        //生成模板内容
        $content = TemplateEngine::build($notify, 'mail');
        Notification::model()->updateAfterContent(array('notify_id' =>$notify['notify_id'],'after_content' => $content ));
        return array(
            'to' => $notify['mail'],
            'title' => $notify['title'],
            'content' => $content,
        );
    }

    /**
     * @param $notify
     * @return Request
     * @throws \Exception
     */
    protected static function mail($notify)
    {
        $apiConfig = static::$api['mail'];
        $templateData = $notify['content']['mail'];
        //生成模板内容
        $content = TemplateEngine::build($notify, 'mail');

        $data = array();
        $data['app_key'] = $apiConfig['app_key'];
        $data['project'] = $apiConfig['project'];
        $data['flag'] = $apiConfig['flag'];
        $data['notify_id'] = $notify['notify_id'];
        $data['username'] = isset($templateData['username']) ? $templateData['username'] : $notify['mail'];
        $data['email'] = $notify['mail'];
        $data['mail_data']['mail_title'] = $notify['title'];
        $data['mail_data']['title'] = $notify['title'];
        $data['mail_data']['mail_content'] = $content;
        $postData = json_encode($data);
        $request = new Request($apiConfig['url'], $apiConfig['method'], $postData);
        $request->setNotification($notify);
        $request->setNotifyType('mail');
        return $request;
    }

    /**
     * @param $notify
     * @return Request|bool
     * @throws \Exception
     */
    protected static function sms($notify)
    {
        $apiConfig = static::$api['sms'];
        $phone = trim($notify['phone']);
        //生成模板内容
        $message = TemplateEngine::build($notify, 'sms');
        $double = explode("/", $phone);
        if ($double) {
            $phone = $double[0];
        }
        $content = str_replace("\n", "+", $message);
        $content = str_replace(" ", "+", $content);
        $content = str_replace("&", "+", $content);
        $params = "notify_id={$notify['notify_id']}&phone={$phone}&smscontent={$content}";
        Notification::model()->updateAfterContent(array('notify_id' =>$notify['notify_id'],'after_content' => $content ));
        //生成GET 请求链接 要在这里处理
        if(!empty($apiConfig['sms_key'][$notify['pusher_id']])){
            $sms_key = $apiConfig['sms_key'][$notify['pusher_id']];
        }else{
            $sms_key = $apiConfig['sms_key']['default'];
        }
        $url = $apiConfig['url'] .'smsKey='.$sms_key.'&'. $params;
        $request = new Request($url, $apiConfig['method']);
        $request->setNotification($notify);
        $request->setNotifyType('sms');
        return $request;
    }

    /**
     * 推送IM 消息.
     * @param $notify
     * @return null
     * @throws \Exception
     */
    protected static function im($notify)
    {
        if (empty($notify['to_id'])) {
            return false;
        }
        $apiConfig = static::$api['im'];
        //生成模板内容
        $message = TemplateEngine::build($notify, 'im');

        $msgData = array(
            'token' => $apiConfig['token'],
            'msg' => $message,
            'user_ids' => $notify['to_id'],
            'msg_type' => 0, //文本消息
            'source' => 'speed', //文本消息
        );
        Notification::model()->updateAfterContent(array('notify_id' =>$notify['notify_id'],'after_content' => $message ));
        $request = new Request($apiConfig['url'], $apiConfig['method'], $msgData);
        $request->setNotification($notify);
        $request->setNotifyType('im');
        return $request;
    }

    /**
     * 设置API配置.
     * @param $type
     * @param $config
     */
    public static function setApi($type, $config)
    {
        static::$api[$type] = $config;
    }
}