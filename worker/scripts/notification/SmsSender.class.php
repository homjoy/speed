<?php

namespace Worker\Scripts\Notification;

use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Request;
use Worker\Package\Notification\RequestFactory;
use Worker\Package\Notification\RequestSender;

/**
 * 短信通知推送脚本.
 * Class SmsSender
 *
 * @package Worker\Scripts\Notification
 */
class SmsSender extends \Frame\Script
{
    protected $send_num = 100; //一次读取的条数
    protected $page_size = 20;//每页发送的条数

    /**
     * @throws \Exception
     */
    public function run()
    {
        $sender = new RequestSender(array($this, 'finish'));
        RequestFactory::setApi('sms', $this->app->config->smsApi);

        //记录发送时间.
        $start = microtime(true);
        $count = 0;
        //开始发送，获取到的脚本会自动标记为发送中.
        $notifications = Notification::model()->pop($this->send_num, Notification::CHANNEL_SMS,'weights');
        if (empty($notifications)) {
            $this->app->response->setBody(date('Y-m-d H:i:s') . "没有需要发送的短信通知.\n");
            return;
        }

        //得到页数,每次发送20条.批量发送,增加发送效率
        $sms_num = count($notifications);
        //页数
        $page_num = ceil($sms_num / $this->page_size);
        for ($page = 0; $page < $page_num; $page++) {
            //先清空之前的发送队列，再继续处理下一批.
            $sender->clearQueue();
            $send_notifications = array_slice($notifications, $page * $this->page_size, $this->page_size);
            if(empty($send_notifications)) continue;
            foreach ($send_notifications as $notify) {
                try {
                    $requests = RequestFactory::build($notify);
                } catch (\Exception $e) {
                    $this->app->log->log('worker/notification/sms_error', array(
                        'message' => $e->getMessage() . PHP_EOL,
                    ));
                    continue;
                }
                $count += count($requests);
                $sender->pushArray($requests);
            }
            try {
                $sender->start();
                $this->app->response->setBody(date('Y-m-d H:i:s') . "发送中...\n");
            } catch (\Exception $e) {
                $this->app->log->log('worker/notification/sms_error', array(
                    'message' => $e->getMessage() . PHP_EOL,
                ));
            }


        }

        $end = microtime(true);
        $total = $end - $start;
        $this->app->response->setBody(date('Y-m-d H:i:s') . "发送结束.总用时：{$total}秒\n");
    }

    /**
     * 发送结束回调处理
     *
     * @param         $output
     * @param         $info
     * @param Request $request
     * @param         $error
     *
     * @return bool
     * @throws \Exception
     */
    public function finish($output, $info, Request $request, $error)
    {
        //$this->app->response->setBody("短信接口返回：$output");
        $notification = $request->getNotification();
        $send_times = intval($notification['send_times']) + 1;
        if ($error || $output != '1') {
            //发送失败
            //记录日志
            $this->app->log->log('worker/notification/sms_error', array(
                'output'     => $output,
                'curl_error' => $error,
            ));
            $output_info = explode(':', $output);
            $return_code = current($output_info);
          
            switch($return_code ){
                case -2:
                    Notification::model()->statusChanged($notification['notify_id'], Notification::STATUS_SENSITIVE_WORDS,$send_times);
                    break;
                case -3:
                    Notification::model()->statusChanged($notification['notify_id'], Notification::STATUS_BLACKLIST,$send_times);
                    break;
                default:
                    if($send_times < Notification::RETRIES){
                        Notification::model()->statusChanged($notification['notify_id'], Notification::STATUS_NOT_SENT,$send_times);
                    }else {
                        Notification::model()->statusChanged($notification['notify_id'], Notification::STATUS_FAILED,$send_times);
                    }
                    break;
                    
            }
            return false;
        }

        Notification::model()->statusChanged($notification['notify_id'], Notification::STATUS_SUCCESS,$send_times);

        return true;
    }
}
