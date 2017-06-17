<?php

namespace Worker\Scripts\Notification;

use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Mailer;
use Worker\Package\Notification\RequestFactory;

/**
 * 邮件通知推送脚本.
 * Class MailSender
 *
 * @package Worker\Scripts\Notification
 * edit by guojiezhu 2015-12-30 优化程序 
 */
class MailSender extends \Frame\Script {

    protected $send_num = 200; //一次读取的条数

    /**
     * @throws \Exception
     */

    public function run() {
        //记录发送时间.
        $start = microtime(true);
        //开始发送，获取到的脚本会自动标记为发送中.
        $notifications = Notification::model()->pop($this->send_num, Notification::CHANNEL_MAIL, 'weights');
        if (empty($notifications)) {
            $this->app->response->setBody(date('Y-m-d H:i:s') . "没有需要发送的邮件通知.\n");
            return;
        }
        foreach ($notifications as $notify) {

            $mail = RequestFactory::buildLocalMail($notify);
            $send_times = intval($notify['send_times']) +1;
            //重试三次
            try {
                //以本地 mail 方式发送通知邮件.
                Mailer::notification($mail);
                //修改通知状态.
                Notification::model()->statusChanged($notify['notify_id'], Notification::STATUS_SUCCESS,$send_times);
            } catch (\Exception $e) {
                $this->app->log->log('worker/notification/mail_error', array(
                    'message' => $e->getMessage() . '重试第' . $send_times . '次' . PHP_EOL,
                ));
                if($send_times < Notification::RETRIES){
                    Notification::model()->statusChanged($notify['notify_id'], Notification::STATUS_NOT_SENT,$send_times);
                }else{
                    Notification::model()->statusChanged($notify['notify_id'], Notification::STATUS_FAILED,$send_times);
                }
            }
        }
        $end = microtime(true);
        $total = $end - $start;
        $this->app->response->setBody(date('Y-m-d H:i:s') . "发送结束.总用时：{$total}秒\n");
    }

}
