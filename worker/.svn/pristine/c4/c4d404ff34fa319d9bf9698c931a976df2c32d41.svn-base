<?php

namespace Worker\Scripts\Test;

use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Request;
use Worker\Package\Notification\RequestFactory;
use Worker\Package\Notification\RequestSender;

class SenderTest extends \Frame\Script {

    public function run() {

        $sender = new RequestSender(array($this,'finish'));
        RequestFactory::setApi('sms',$this->app->config->smsApi);
        RequestFactory::setApi('mail',$this->app->config->mailApi);

        $start = microtime(true);
        $count = 0;
        $notifications = Notification::model()->pop(30);
        do{
            foreach($notifications as $notify)
            {
                try{
                    $requests = RequestFactory::build($notify);
                }catch (\Exception $e){
                    var_dump($e->getMessage());
                    continue;
                }
                $count += count($requests);
                $sender->pushArray($requests);
            }

            try{
                $sender->start();
                $this->app->response->setBody("ok");
            }catch (\Exception $e){
                $this->app->response->setBody($e->getMessage().PHP_EOL);
            }
            $notifications = Notification::model()->pop(20);
        }while(!empty($notifications) && $count < 100);



        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("总用时：{$total}秒");
    }

    /**
     * 发送结束回调处理
     * @param $output
     * @param $info
     * @param Request $request
     * @param $error
     * @return bool
     * @throws \Exception
     */
    public function finish($output,$info,Request $request,$error)
    {
        $notification = $request->getNotification();
        if($error){
            //发送失败
            //记录日志
            $this->app->log->log('notification_error', array(
                'curl_error' => $error,
            ));
            Notification::model()->statusChanged($notification['notify_id'],Notification::STATUS_FAILED);
            return false;
        }
        //TODO 其他返回值.
        if(strtolower(trim($output)) == 'ok'){
            //发送成功
            //$request->getNotifyType()
            Notification::model()->statusChanged($notification['notify_id'],Notification::STATUS_SUCCESS);
        }else{
            //发送失败
            //记录日志
            $this->app->log->log('notification_error', array(
                'type' => $request->getNotifyType(),
                'data' => $request->getData(),
                'notification' => $notification,
            ));
            Notification::model()->statusChanged($notification['notify_id'],Notification::STATUS_FAILED);
        }

        return true;
    }
}
