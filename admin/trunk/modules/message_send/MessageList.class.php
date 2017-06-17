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
class MessageList extends BaseModule {

    protected $errors = null;
    private $params = null;
    private $page_size = 20;
    protected $notify_type = array(
        1 =>'邮件',
        2 =>'短信',
        4 => 'im',

    );

    protected $checkUserPermisssion = TRUE;
    public function run()
    {
        $this->_init();
        //page  count  notify_type   data search_params
        if(empty($this->params['op_user_id'])){
            unset($this->params['op_user_id']);
        }
        $this->params['offset']  = $this->params['page'];
        $this->params['limit']  =  $this->page_size;
        $data =  '';
        $data =  MessageSend::getInstance()->getMessageList($this->params);

        //只给前端输出想要的字段
        $end_data= $temp =array();
        if(!empty($data)&&is_array($data)){
            foreach($data as &$value){
                $temp['msg_id'] = isset($value['msg_id'])?$value['msg_id']:'';
                $temp['channel'] = isset($this->notify_type[$value['channel']])?$this->notify_type[$value['channel']]:'';
                $temp['content'] = isset($value['content'])? $value['content']:'';
                $temp['title'] = isset($value['title'])?$value['title']:'';
                $temp['op_user_id'] = isset($value['op_user_id'])?$value['op_user_id']:'';
                $temp['send_object'] = isset(MessageSend::$sendObject[$value['send_object']])?MessageSend::$sendObject[$value['send_object']]:'';
                $temp['send_at'] = isset($value['send_at'])?$value['send_at']:'';
                $temp['update_time'] = isset($value['update_time'])?$value['update_time']:'';

                $temp['weights'] = isset(MessageSend::$weights[$value['weights']])?MessageSend::$weights[$value['weights']]:'';
                if(isset($value['send_status'])){
                    $temp['send_status'] =  isset(MessageSend::$sendStatus[$value['send_status']])?MessageSend::$sendStatus[$value['send_status']]:'';
                }
                $end_data[] =   $temp;

            }
        }
        $return =  Response::gen_success($end_data);
        $this->params['count'] =1;
        $count =  MessageSend::getInstance()->getMessageList($this->params);

        if($count){
            $return['count'] = ceil($count/$this->page_size);
        }else{
            $return['count']= 0;
        }
        $return['page'] = $this->params['page'];
        if(is_array($this->params['channel'])&&count($this->params['channel'])==1){
            $this->params['channel'] =array_pop($this->params['channel']);
        }
        if(!empty( $this->params ['to_id'])) {
            $user_info = UserInfo::getInstance()->getUserInfo(array('user_id' => $this->params ['to_id']));
            $current_user_info = current($user_info);
            $return['search_user_info'][] = array('name' => $current_user_info['name_cn'],'user_id' =>$current_user_info['user_id'] );

        }else{
            $return['search_user_info'][] = array();
        }
        $return['search_params'] = $this->params;
        $return['notify_type'] =$this->notify_type;
        $this->app->response->setBody($return);

    }

    function deep_foreach ($arr, $k='', $pre_indent = '') {
        $return_str  = '';
        if (!is_array ($arr)) {
            return $return_str;
        }

        $str = $k ? "[$k] => " : '';
        $cur_indent = $pre_indent . " ";

        $return_str .= $pre_indent.$str."Array$pre_indent(<br/>";

        foreach ($arr as $key => $val ) {
            if (is_array ($val)) {
                $return_str .=$this->deep_foreach ($val, $key, $cur_indent);
            } else {
                $return_str .= $cur_indent."[$key] = > ".$val.'<br/>';
            }
        }

        $return_str .= $pre_indent.")<br/>";
        return $return_str;
    }

    private function _init()
    {//path  father_id

        $this->rules = array(
            'page' => array(
                'type'    => 'integer',
                'default' => 1
            ),
            'op_user_id' => array(
                'type'    => 'integer',
            ),
            'channel'=> array(
                'type'=>'multiId',
                'default'=>array(1,2,4),
            ),
            'status'=> array(
                'type'=>'multiId',
                'default'=>array(1,0),

            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }




}