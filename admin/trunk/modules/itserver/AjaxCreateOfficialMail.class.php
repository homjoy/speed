<?php
namespace Admin\Modules\Itserver;
use Admin\Package\Log\Log;
use Admin\Package\Itserver\Itserver;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Core\UserAccount;
/**
 *
 * hongzhou@meilishuo.com
 * 2015-01-19
 */

class AjaxCreateOfficialMail extends  BaseModule {
    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    const IT =8;
    protected $token = '747e8e9223dee90fb480685ded958713';
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL = 5; //邮箱密码修改登记
    public static $TYPE_WIFI= 6;
    public function run() {
       $this->_init();
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

       $result = Itserver::getInstance()->checkMail(
           array(
               'email'=>$this->params['mail_name'].'@meilishuo.com',
               'depart_id'=>!empty($this->params['depart_id']) ? $this->params['depart_id']:0,
            )
       );

       if(empty($result)){
           $return = Response::gen_error(50001,'','邮箱不可用请替换,找IT核对');
           return $this->app->response->setBody($return);
       }
        //默认为独立账号 不可以与speed账号 冲突则hr一键注册
        if( Itserver::getInstance()->getOfficialMail(
            $this->params
        )){
            $return = Response::gen_error(50001,'','邮箱不可用请替换,找Hr核对');
            return $this->app->response->setBody($return);
        }

        $user =array();
        $user =$this->getUser(
            array(
                'mail'=>$this->params['mail_name'],
                'limit'=>count($this->params['mail_name'])
            )
        );
        if(empty($this->params['mobile'])){
            $this->params['mobile'] = isset($user['mobile'])?$user['mobile']:'';
            if(empty($this->params['mobile'])){
                return $this->app->response->setBody(Response::gen_error(10001,'','手机号不能为空'));
            }
        }
        if(empty($this->params['name_cn'])){
            $this->params['name_cn'] = (isset($user['name_cn']) && !empty($user['name_cn']))?$user['name_cn']:'无';
        }

        $result = array();
        $result = Itserver::getInstance()->createMail(
            array(
                'email' => $this->params['mail_name'].'@meilishuo.com',
                'depart_id' => 100000,
                'truename' => $this->params['name_cn'],
            )
        );

        if(empty($result)){
            $return = Response::gen_error(50005,'','it邮箱创建失败');
            return $this->app->response->setBody($return);
        }


        //短信
        $sms =array();
        $info=null;
        if( isset($result['passwd'])){
            $info.='mail密码为'.$this->params['mail_name'].'mail密码为'.$result['passwd'];
            //创建成功后短信通知
            $sms['content']=array('mail'=>$this->params['mail_name'],'msg'=>$info);
            $sms['mobile'] =$this->params['mobile'];
            $sms['mail'] =$this->params['mail_name'];
            $this->sendSms($sms);
        }

        $speed_result = Itserver::getInstance()->createOfficialMail(
            $this->params
        );
        if(empty($speed_result)){
            $return = Response::gen_error(50005,'','SPEED邮箱创建失败,短信已下发');
            return $this->app->response->setBody($return);
        }

        $this->doLog($this->params);
        $this->app->response->setBody(Response::gen_success('创建成功'));
    }

    //发短信
    private function sendSms($param = array()) {
        if(!isset($param['content'])||empty($param['mobile'])|| !isset($param['mail'])) {
            return FALSE;
        }
        $date = date('Y-m-d H:i:s',time());
        $message['sms'] =$param['content'];
        $array_data= array(
            'token' => $this->token,
            'custom_id' => '1',
            'content' => $message,
            'to_id' => $this->user['id'], 'send_at' => $date, 'title' => '注册独立mail账号', 'template_id' => 124, 'channel' => 'sms', 'phone' =>$param['mobile'],'mail'=>$param['mail']);
        $return = $this->getClient()->call('worker','notification/push',$array_data);
        $return = $this->parseApiData($return);
        //记录日志TODO
        $array_data['return'] =$return;
        $this->app->log->log('admin/itserver/ajax_create_official_mail', json_encode($array_data));
        return $array_data;

    }
    protected function doLog($new_param=array(),$old_param='create'){
        $ret = Log::getInstance()->createLogs(array(
                'user_id'=>$this->user['id'],
                'handle_id'=>100000,
                'operation_type'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>self::IT
            )
        );
        return $ret;
    }
    private function getUser($param=array()) {//user_id
        $ret =array();
        $ret_mail = UserInfo::getInstance()->getUserInfo( $param);;
        $ret_mail = is_array($ret_mail)?array_pop($ret_mail):'';
        $ret_mobile = PersonalInfo::getInstance()->getPersonalInfo($param);
        $ret_mobile = is_array($ret_mobile)?array_pop($ret_mobile):'';
        $ret['mail'] = isset($ret_mail['mail'])?$ret_mail['mail']:'';
        $ret['name_cn'] = isset($ret_mail['name_cn'])?$ret_mail['name_cn']:'';
        $ret['depart_id'] = isset($ret_mail['depart_id'])?$ret_mail['depart_id']:'';
        $ret['mobile'] = isset($ret_mobile['mobile'])?$ret_mobile['mobile']:'';
        return $ret;
    }
    private function _init() {

        $this->rules  = array(

            'mail_name' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
            ),
            'mobile' => array(
                'type'=>'string',
            ),
            'name_cn' => array(
                'type'=>'string',
            ),

        );

        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();

    }
}
