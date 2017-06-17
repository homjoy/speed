<?php
namespace Admin\Modules\Itserver;
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
class AjaxUpdatePwd extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    const mail_string = '@meilishuo.com';
    public $length =2;
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL = 5; //邮箱密码修改登记
    public static $TYPE_WIFI= 6;
    protected $token = '747e8e9223dee90fb480685ded958713';
    const IT =8;
    protected $mail_minus = 7603200;
    public function run() {
        $this->_init();

        //校验参数
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
       //生成密码

        $this->params['pwd'] = Itserver::getInstance()->make_password($this->length);


        $check = Itserver::getInstance()->complexMatch( $this->params['pwd'] ,$this->params['login_name'] );
        //密码校验
        if(isset($check['code'])&&$check['code']!=200){
            return $this->app->response->setBody($check);
        }
        //获取用户信息
        $user =$this->getUser(
            array(
                'mail'=>$this->params['login_name']
            )
        );

        if(empty($this->params['mobile'])){
            $this->params['mobile'] = isset($user['mobile'])?$user['mobile']:'';
            if(!$this->params['mobile']){
                return $this->app->response->setBody(Response::gen_error(10003, '', '手机号为空,请详细检查'));
            }
        }

        //获取操作者
        $user_mail = UserInfo::getInstance()->getUserInfo(
            array(
                'user_id'=>$this->user['id']
            )
        );
        $user_mail = is_array($user_mail)?array_pop($user_mail):'';
        $user_mail = isset($user_mail['mail'])?$user_mail['mail']:'';


        //构造参数
       $ret=$msg= $get='';
        switch($this->params['type']){
            case self::$TYPE_MAIL:
                $is_mail = preg_match('/@/', $this->params['login_name']);
                if (!$is_mail) { // 邮箱
                    $this->params['login_name'] .=self::mail_string;
                }
                $ret = Itserver::getInstance()->updateMailPwd(
                    array(
                        'mail_name'=>  $this->params['login_name'],
                        'mail_pwd'=>  $this->params['pwd'],
                    )
                );
                if($ret){
                    $msg = '邮箱用户名:'.$this->params['login_name'].'密码:'.$this->params['pwd'];

                    $get =UserAccount::getInstance()->getAccount(
                        array(
                            'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                            'account_type'=>self::$TYPE_MAIL,
                        )
                    );


                    if($get){
                        $time  =time();
                        $time  = $time - $this->mail_minus;
                        $get = is_array($get)?array_pop($get):'';
                        $update_mail =UserAccount::getInstance()->updateAccount(
                            array(
                                'id'=>isset($get['id'])?$get['id']:'',
                                'account_type'=>self::$TYPE_MAIL,
                                'status'=>1,
                                'update_time'=>date('Y-m-d H:i:s',$time)
                            )
                        );
                        if(empty($update_mail)){
                            //log

                            $this->doLog(    array(
                                'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                                'account_type'=>self::$TYPE_MAIL,
                                'login_name'=>isset($user['mail'])?$user['mail']:'',
                                'status'=>1,
                            ),'update','邮箱更新时间失败');
                        }
                    }
                }

                break;
            case self::$TYPE_VPN:
                $ret = Itserver::getInstance()->updateVpnPwd(
                   array(
                       'op'=>$user_mail,
                       'p'=>$this->params['pwd'],
                       'u'=>$this->params['login_name'],
                   )
                );
                if($ret){
                    $msg = 'vpn用户名:'.$this->params['login_name'].'密码:'.$this->params['pwd'];
                    $get =UserAccount::getInstance()->getAccount(
                        array(
                            'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                            'account_type'=>self::$TYPE_VPN,
                        )
                    );
                    if($get){
                        $time  =time();
                        $time  = $time - $this->mail_minus;

                        $update_vpn =UserAccount::getInstance()->createAccount(
                            array(
                                'id'=>isset($get['id'])?$get['id']:'',
                                'account_type'=>self::$TYPE_VPN,
                                'status'=>1,
                                'update_time'=>date('Y-m-d H:i:s',$time)
                            )
                        );
                        if(empty($update_vpn)){
                            //log

                            $this->doLog(    array(
                                'user_id'=>isset($user['user_id'])?$user['user_id']:'',
                                'account_type'=>self::$TYPE_VPN,
                                'login_name'=>isset($user['mail'])?$user['mail']:'',
                                'status'=>1,
                            ),'update','vpn更新时间失败');
                        }
                    }
                }

                break;

            case self::$TYPE_WIFI:

                $ret = Itserver::getInstance()->updateWifiPwd(
                    array(
                       'op'=>$user_mail,
                       'p'=>$this->params['pwd'],
                       'u'=>$this->params['login_name'],
                    )
                );
                if($ret){
                    $msg = 'wifi用户名:'.$this->params['login_name'].'密码:'.$this->params['pwd'];
                }

                break;

        }

        if(!$ret){
            return $this->app->response->setBody(Response::gen_error(50001,'','修改失败'));
        }
        //创建成功后短信通知
        $sms['content']=array('mail'=>$this->params['login_name'],'msg'=>$msg);
        $sms['mobile'] =$this->params['mobile'];
        $sms['mail'] =$this->params['login_name'];

        //日志
        $this->doLog($this->params);
        //短信
        if( !$this->sendSms($sms)) {
            return $this->app->response->setBody(Response::gen_error(50001,'修改密码成功，由于手机或用户名不全短信发送失败'));
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
        $array_data['return'] =$return;
        $this->app->log->log('admin/itserver/ajax_update_pwd', json_encode($array_data));
        return $array_data;

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
            'login_name' => array(
                'type'=>'string',
                'required' => TRUE,
                'allowEmpty' => FALSE,
            ),
            'type' => array(
                'type'=>'integer',
                'required' => TRUE,
                'allowEmpty' => FALSE,
            ),
            'mobile' => array(
               'type'=>'string',

           )
        );

        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();

    }
    
}