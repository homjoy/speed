<?php

namespace Admin\Modules\Message_send;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Message_send\MessageSend;
use  Libs\Util\ArrayUtilities;
/**
 * MessageSend 信息发送界面
 * guojiezhu@meilishuo.com
 * 2016-02-25
 */
class MessageUpSert extends BaseModule {
    protected $all = 1;
    protected $status = 1;
    protected $checkUserPermission = TRUE;
    protected $params;
    public function run() {
        $this->__init();
        $return = array();
        if(!empty($this->params['msg_id'])) {

            $return = MessageSend::getInstance()->getMessageList($this->params);
            if(!$return){
                $return = Response::gen_error(50001,'','获取信息失败');
            }else{
                $return = is_array($return)?array_pop($return):'';
                if(isset($return['msg_id'])){
                    $temp =   MessageSend::getInstance()->getMessageUserList(
                        array(
                            'msg_id'=> $return['msg_id'],
                            'all'=>1
                        )
                    );

                    $mail =  ArrayUtilities::my_array_column($temp,'mail');
                    $mail =array_filter($mail);
                    $phone =  ArrayUtilities::my_array_column($temp,'phone');
                    $phone =array_filter($phone);
                    //这里不做兼容性处理  因为ajax接口不支持双添 日后可以做个兼容
                    if(!empty($mail)){
                        $return['send_user']  = $mail;
                    }elseif(!empty($phone)){
                        $return['send_user'] = $phone;
                    }else{
                        $return['send_user'] = array();
                    }
                    foreach( $return['send_user'] as &$value){
                        $value .="&#13;&#10;";
                    }

                }

                $return = Response::gen_success($return);
            }

        }else{
            $return = Response::gen_success($return);
        }

        $return['weights'] = MessageSend::$weights;
        $return['send_object'] = MessageSend::$sendObject;
        $return['status'] = MessageSend::$status;
//        var_dump($return);die();
        $this->app->response->setBody($return);
    }

    public function __init(){
        $this->rules = array(
            'msg_id' => array(
                'type'    => 'integer',
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }


}