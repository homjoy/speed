<?php

namespace Atom\Scripts\Approval;

use Libs\Util\ArrayUtilities;
use Atom\Package\Approval\OrderSendQueue;
use Atom\Package\User\UserInfo;
use Atom\Package\Scripts\LeaveSendMessage;
use Atom\Package\Scripts\VisitingCardSendMessage;
use Atom\Package\Scripts\ExpressSendMessage;
use Atom\Package\Scripts\OfficeSendMessage;
use Atom\Package\Scripts\AssetsSendMessage;
/**
 * 单据发送消息提醒
 * Class OrderSendMessage
 * @package Atom\Scripts\Approval
 */
class OrderSendMessage extends \Frame\Script{
    public function run(){
        //查询单据队列表信息

        $queryData = array(
            'status'=>0,//未发送
        );
        $records = OrderSendQueue::model()->getList($queryData);
        if(!empty($records)){
            foreach($records as $r){
                $p = array(
                    'id' => $r['id'],
                    'status' => 1,//发送中
                );
                OrderSendQueue::model()->insertOrUpdate($p);

                switch($r['order_type']){
                    case '1':   //请假
                        LeaveSendMessage::getInstance()->sendMessage($r);
                        break;
                    case '2':   //名片
                        VisitingCardSendMessage::getInstance()->sendMessage($r);
                        break;
                    case '3':   //快递
                        ExpressSendMessage::getInstance()->sendMessage($r);
                        break;
                    case '4':   //办公用品
                        OfficeSendMessage::getInstance()->sendMessage($r);
                        break;
                    case '5':   //固定资产
                        AssetsSendMessage::getInstance()->sendMessage($r);
                        break;
                    default:
                        break;
                }
                $p = array(
                    'id' => $r['id'],
                    'status' => 10,//发送成功
                );
                OrderSendQueue::model()->insertOrUpdate($p);
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
