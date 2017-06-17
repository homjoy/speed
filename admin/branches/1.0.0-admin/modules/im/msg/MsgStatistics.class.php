<?php
namespace Admin\Modules\Im\Msg;
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Im\ImMsg;
use Admin\Package\Im\ImPublicMsg;

class MsgStatistics  extends BaseModule { 

    public function run(){
        $res = array(
            'msgCount' => 0,
            'msgPublicCount' => 0,
        );
        //统计每天的消息树
        $params = array(
            'between_ctime' => array(
                //'begin' => date('Y-m-d 00:00:00', time()),
                'begin' => date('2014-11-09 00:00:00', time()),
                //'end' => date('Y-m-d 23:59:59', time()),
                'end' => date('2014-11-09 23:59:59', time()),
            )
        );
        $res['msgCount'] =  ImMsg::getInstance()->countByPararm($params);
        $res['msgPublicCount'] =  ImPublicMsg::getInstance()->countByPararm($params);
    
        //var_dump($res);exit;
        //每天多少人发消息
       // $res['public_msg_num'] = ImMsg::getPublicMsgNumByPararm($params);

        $this->app->response->setBody($res);

    }
    
}
