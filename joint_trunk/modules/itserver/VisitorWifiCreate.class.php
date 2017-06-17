<?php
namespace Joint\Modules\Itserver;

use Frame\Speed\Lib\Api;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Joint\Package\Utils\VisitorWifi;

/**
 * wifi创建
 * @author hongzhou@meilishuo.com
 * @date 2015-9-02
 */
class VisitorWifiCreate  extends BaseModule{
    protected $errors = NULL;
    private   $params = NULL;

    public function run() {

        $this->_init();
        $is_mail = preg_match('/@/', $this->params['interview']);
        if ($is_mail) { // 邮箱
            $is_mail = explode("@",$this->params['interview']);
            $this->params['interview'] = $is_mail[0];
        }
        //请求atom接口
        $q=array();

        $user_info=array();
        $user_info   =   Api::atom('account/get_user_info',array('mail'=>$this->params['interview']),true);

        if(!isset($user_info['code'])|| $user_info['code']!=200){
            return  $this->app->response->setBody(Response::gen_error(20001,'邮箱不匹配'));
        }
        if(isset($user_info['data'])){
            $user_info =array_pop($user_info['data']);
        }

        $q=array(
            'user_id'=> isset($user_info['user_id'])?$user_info['user_id']:'',
            'create_time'=>@date('Y-m-d H:i:s',time()),
            'visitor_name'=>$this->params['visitor_name'],
            'visitor_mobile'=>$this->params['visitor_mobile'],
            'interview'=>$this->params['interview'],
            'id'=>$this->params['id'],
            'handle_status'=>$this->params['handle_status'],
            'status'=>$this->params['status'],
        );

        $ret =  Api::atom('itserver/visitor_wifi_create',$q,true);
        if (!isset($ret['code'])|| $ret['code']!=200) {
            $return = Response::gen_error(30001,'','创建失败');
        }else{
            $return = $ret;
        }
         return  $this->app->response->setBody($return);


    }
    private function _init() {

        $this->rules = array(
            'visitor_name' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'visitor_mobile' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'interview' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'id' => array(
                'type'=>'integer',
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'maxLength'=> 30
            ),
            'handle_status' => array(
                'type'=>'integer',
                'default'=>0,
            ),
            'status' => array(
                'type'=>'integer',
                'default'=>1,
            ),

        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }



}
