<?php

namespace Admin\Modules\Message_send;
use Libs\Util\ArrayUtilities;
use Admin\Package\Account\UserInfo;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Message_send\MessageSend;

/**
 * MessageSend 消息推送列表
 * guojiezhu@meilishuo.com
 * 2016-02-25
 */
class MessageUserList extends BaseModule {
    protected $errors = NULL;
    private $params = NULL;
    private   $page_size = 20;
    private   $count = 1;
    protected $checkUserPermisssion = TRUE;
    protected $send_status = array(
        0 => '未发送',
        1 => '发送中',
        2 => '发送取消',
        3 => '消息丢弃',
        9 => '发送失败',
        10 => '发送成功',
        4=>'敏感词',
        5 =>'黑名单',

    );
    public function run() {
        $this->_init();
        if ($this->query()->hasError()) {
            $return =  Response::gen_success(array());
            $return['count'] =0;
            $return['page'] =1;
            return $this->app->response->setBody($return);
        }

            //分页控
        $this->params['offset']  = $this->params['page'];
        $this->params['limit']  = $this->page_size;
        //count获取总条数
        $this->params=array_filter($this->params);

        if(empty($this->params['count'])){
            $this->params['count'] =$this->count;
        }
        $count = MessageSend::getInstance()->getMessageUserList($this->params);

        if ($count == FALSE) {//容错
            $return =  Response::gen_success(array());
            $return['count'] =0;
            $return['page'] =1;
            return $this->app->response->setBody($return);
        }
        unset( $this->params['count']);
        //获取当前20条数据

        $data = MessageSend::getInstance()->getMessageUserList($this->params);
        if(!empty($data)&&is_array($data)){
            foreach($data as &$value){
                if(isset($value['status'])){
                    $value['status'] =  isset($this->send_status[$value['status']])?$this->send_status[$value['status']]:'';
                }
            }
        }
        $return = Response::gen_success($data);//返回总数
        $return['count'] = ceil($count/$this->page_size);
        $return['page'] = $this->params['page'];
        $this->app->response->setBody($return);

    }

    private function _init() {

        $this->rules = array(

            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'msg_id'=>array(
                'type'=>'integer',
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'default' => ''
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }



}