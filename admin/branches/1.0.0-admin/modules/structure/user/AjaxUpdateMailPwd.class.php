<?php
namespace Admin\Modules\Structure\User;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Itserver\Itserver;
use Admin\Package\Log\Log;
use Admin\Package\Core\UserAccount;
/**
 * 修改邮箱密码
 * @author hongzhou@meilishuo.com
 * @since 2015-11-14 下午12:53:13
 */
class AjaxUpdateMailPwd extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public static $OFFICE_MAIL = 1;
    public static $TYPE_MAIL_PWD = 5;
    protected $token = '747e8e9223dee90fb480685ded958713';
    const mail_string = '@meilishuo.com';
    protected $mail_minus = 7603200;//88天 两天后新建mail不改密码失效
    public $length =2;
    const IT =8;

    public function run() {
        $this->_init();
        //校验参数
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
       //生成密码
        if(empty($this->params['mail_pwd'])){
            $this->params['mail_pwd'] = Itserver::getInstance()->make_password($this->length);
        }

        $msg = '用户名:'.$this->params['mail_name'].'密码:'.$this->params['mail_pwd'];
        $check = Itserver::getInstance()->complexMatch( $this->params['mail_pwd'] ,$this->params['mail_name'] );
        //密码校验
        if(isset($check['code'])&&$check['code']!=200){
            return $this->app->response->setBody($check);
        }
        //获取用户信息
        $user =array();
        $user = $this->getUser(
            array(
                'mail'=>$this->params['mail_name']
            )
        );
        //创建成功后短信通知
        $sms['content']=array('mail'=>$this->params['mail_name'],'msg'=>$msg);
        $sms['mobile'] =isset($user['mobile'])?$user['mobile']:'';
        $sms['mail'] =$this->params['mail_name'];

        //构造参数
        $is_mail = preg_match('/@/', $this->params['mail_name']);
        if (!$is_mail) { // 邮箱
            $this->params['mail_name'] .=self::mail_string;
        }
        $ret = $this->updateMailPwd($this->params);

        if(!$ret){
            return $this->app->response->setBody(Response::gen_error(50001,'修改失败'));
        }


        //日志
        $this->doLog($this->params);
        //短信
         if(!$this->sendSms($sms)){
                return $this->app->response->setBody(Response::gen_error(50001,'修改密码成功，由于手机或用户名不全短信发送失败'));
         }

        //过期处理
        $get =UserAccount::getInstance()->getAccount(
            array(
                'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                'account_type'=>self::$TYPE_MAIL_PWD,
            )
        );
        if($get){
            $time  =time();
            $time  = $time - $this->mail_minus;

            $update_mail =UserAccount::getInstance()->updateAccount(
                array(
                    'id'=>isset($get['id'])?$get['id']:'',
                    'account_type'=>self::$TYPE_MAIL_PWD,
                    'status'=>1,
                    'update_time'=>date('Y-m-d H:i:s',$time)
                )
            );
            if(empty($update_mail)){
                //log

                $this->doLog(    array(
                    'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                    'account_type'=>self::$TYPE_MAIL_PWD,
                    'login_name'=>isset($user['mail'])?$user['mail']:'',
                    'status'=>1,
                ),'update','邮箱入库失败');
            }
        }

        $this->app->response->setBody(Response::gen_success('修改成功'));
    }
    private function getUser($param=array()) {//user_id
        $ret =array();
        $ret_mail = UserInfo::getInstance()->getUserInfo( $param);;
        $ret_mail = is_array($ret_mail)?array_pop($ret_mail):'';
        $ret['mail'] = isset($ret_mail['mail'])?$ret_mail['mail']:'';
        $ret['user_id'] = isset($ret_mail['user_id'])?$ret_mail['user_id']:'';
        $ret_mobile = PersonalInfo::getInstance()->getPersonalInfo(
            array(
                'user_id'=>  $ret['user_id']
            )
        );
        $ret_mobile = is_array($ret_mobile)?array_pop($ret_mobile):'';
        $ret['mobile'] = isset($ret_mobile['mobile'])?$ret_mobile['mobile']:'';

        return $ret;
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
            'to_id' => $this->user['id'], 'send_at' => $date, 'title' => '修改账号密码', 'template_id' => 124, 'channel' => 'sms', 'phone' =>$param['mobile'],'mail'=>$param['mail']);
        $return = $this->getClient()->call('worker','notification/push',$array_data);
        $return = $this->parseApiData($return);
        //记录日志TODO
        $array_data['return'] =$return;
        $this->app->log->log('admin/structure/user/ajax_get_all_auth', json_encode($array_data));
        return $array_data;

    }

    protected function updateMailPwd($mail){
        $return_data=NULL;
        $return_data = $this->getClient()->call('joint', 'itserver/email_update', $mail);

        $return_data = $this->parseApiData($return_data);
        return $return_data;

    }
    protected function doLog($new_param=array(),$old_param='update'){
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
    private function _init() {

        $this->rules  = array(
            'mail_pwd' => array(
                'type'=>'string',
            ),
            'mail_name' => array(
               'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
           ),
        );

        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();

    }
    
}