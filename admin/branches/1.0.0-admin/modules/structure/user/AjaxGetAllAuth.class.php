<?php
namespace Admin\Modules\Structure\User;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Itserver\Itserver;
use Admin\Package\Log\Log;
use Admin\Package\Account\UserInfo;
use Admin\Package\Department\Department;
use Admin\Package\Core\UserAccount;
use Admin\Package\Account\PersonalInfo;
use Admin\Package\Mail\MailGroupDepartRelation;
use Admin\Package\Mail\MailGroup;
/**
 * 一键注册
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class AjaxGetAllAuth extends BaseModule {
    private $params = array();
    protected $errors = array();
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记
    public static $TYPE_WIFI = 6; //邮箱密码修改登记
    public static $IT= 8; // 日志记录类型
    public static $VIEW_SWITCH_JSON = TRUE;
    protected $token = '747e8e9223dee90fb480685ded958713';
    protected $mail_minus = 7603200;//88天 两天后新建mail不改密码失效
    public function run() {

        $this->_init();
        //校验前台传过字段
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        $user_mail= $ret =$info =$is_mail= $msg =array();
        //当前用户
        if(!isset($this->user['id'])){
            return $this->app->response->setBody(Response::gen_error(10002, '登录失败'));
        }
        //检验后台必须存有字段
        $ret  =$this->getUser(array('user_id'=>$this->params['user_id']));
        if(empty($ret['mail'])|| empty($ret['mobile'])||empty($ret['name_cn']) || empty($ret['depart_id'])){
            return $this->app->response->setBody(Response::gen_error(10003, '用户手机号、邮箱和部门必须保存哦'));
        }
        //构造创建所需字段
        $is_mail = preg_match('/@/', $ret['mail']);
        if (!$is_mail) { // 邮箱
            $is_mail =  $ret['mail'];
            $is_mail .='@meilishuo.com';
        }else{
            $is_mail = $ret['mail'];
        }
        $user_mail = UserInfo::getInstance()->getUserInfo( array('user_id'=>$this->user['id']));
        $user_mail = is_array($user_mail)?array_pop($user_mail):'';
        $user_mail = isset($user_mail['mail'])?$user_mail['mail']:'';

        //check
        $query_params =$this->params;
        $query_params['login'] =$ret['mail'];
        $query_params['email'] =$is_mail;
        $query_params['depart_id'] =$ret['depart_id'];
        $query_params['mail'] =$is_mail;
        if($this->checkIsCreate($query_params)==FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'未通过检测请换邮箱'));
        }

        $msg='您创建的账号名为'.$ret['mail'];
        $query_params =$this->params;
        $query_params['mail'] =$is_mail;
        $query_params['user_mail'] =$user_mail;
        $query_params['depart_id'] =$ret['depart_id'];
        $query_params['email'] =$is_mail;
        $query_params['truename'] =$ret['name_cn'];
        $query_params['name_cn'] =$ret['name_cn'];

        $temp = $this->createItserver($query_params);

        if($temp['sms']==FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'无可创建信息'));
        }
        $info =$this->assembleInfo($this->params);

        //创建成功后短信通知
        $sms['content']=array('mail'=>$ret['mail'],'msg'=>$msg.$temp['sms']);
        $sms['mobile'] =$ret['mobile'];
        $sms['mail'] =$ret['mail'];
        $sms['user_id'] = isset($this->params['user_id'])?$this->params['user_id']:0;
        $this->sendSms($sms);
        $this->doLog($this->params,'add',$info.'成功创建:'.$temp['check']);

        $this->app->response->setBody(Response::gen_success(array(
            'msg'=>$info.'创建成功:'.$temp['check'],
        )));
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
            'to_id' => $param['user_id'],'from_id' =>$this->user['id'] , 'send_at' => $date, 'title' => '一键注册', 'template_id' => 124, 'channel' => 'sms', 'phone' =>$param['mobile'],'mail'=>$param['mail']);
        $return = $this->getClient()->call('worker','notification/push',$array_data);
        $return = $this->parseApiData($return);
        //记录日志TODO
        $array_data['return'] =$return;
        $this->app->log->log('admin/user/ajax_get_all_auth', json_encode($array_data));

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
    /****
     * @param array $param
     * @return bool
     * 检验
     */
    private function checkIsCreate($param =array()) {

        //mail
       if(isset($param['mail_status'])&&$param['mail_status']==1){
          $mail = Itserver::getInstance()->checkMail($param);//email depart_id

          if(!$mail){
              return false;
          }

        }
        //redmine
//        if($param['redmine_status']==1){
//            $redmine = Itserver::getInstance()->checkRedmine($param);//mail login
//            if(!$redmine){
//                return false;
//            }
//        }
        //vpn wifi
        return TRUE;
    }
    /***
     * @param array $param
     * 组装信息
     *
     */
    private function assembleInfo($param=array()) {
        $msg ='创建账号';
        if(isset($param['vpn_status'])&&$param['vpn_status']==1){//user_mail mail

            $msg .='vpn ';
        }
        if(isset($param['wifi_status'])&&$param['wifi_status']==1){//mail user_mail

            $msg .='wifi ';
        }
        if(isset($param['redmine_status'])&&$param['redmine_status']==1){//mail

            $msg .='redmine ';
        }
        if(isset($param['mail_status'])&&$param['mail_status']==1){//email depart_id truename

            $msg .='mail ';
        }
        if(isset($param['computer_status'])&&$param['computer_status']==1){
            $msg .='computer ';
        }
        RETURN $msg;
    }
    /***
     * @param array $param
     * 创建
     *
     */
    private function createItserver($param=array()) {
        $sms =$check ='';

        if(isset($param['vpn_status'])&&$param['vpn_status']==1){
            $vpn = Itserver::getInstance()->createVpn($param);
            if(!empty($vpn)&&isset($vpn['pwd'])){
                $sms.=' vpn密码为'.$vpn['pwd'].';';
                $check .='vpn ';
                //get
                $get =UserAccount::getInstance()->getAccount(
                        array(
                            'user_id'=>$param['user_id'],
                            'account_type'=>self::$TYPE_VPN,

                        )
                );
                if(empty($get)){
                    //创建
                    $create_vpn =UserAccount::getInstance()->createAccount(
                        array(
                            'user_id'=>$param['user_id'],
                            'account_type'=>self::$TYPE_VPN,
                            'login_name'=>$param['mail'],
                            'status'=>1,
                        )
                    );
                    if(empty($create_vpn)){
                        //log
                        $this->doLog(  array(
                            'user_id'=>$param['user_id'],
                            'account_type'=>self::$TYPE_VPN,
                            'login_name'=>$param['mail'],
                            'status'=>1,
                        ),'add','vpn入库失败');
                    }
                }

            }else{
                //log
                $this->doLog( $param,'add','VPNIT端创建失败');
            }

        }
        if(isset($param['wifi_status'])&&$param['wifi_status']==1){
            $wifi = Itserver::getInstance()->createWifi($param);
            if(!empty($wifi)&&isset($wifi['pwd'])){
                $sms.='wifi密码为'.$wifi['pwd'].';';
                $check .='wifi ';
            }else{
                $this->doLog($param,'add','VPNIT端创建失败');
            }

        }

        if(isset($param['redmine_status'])&&$param['redmine_status']==1){

            $red = Itserver::getInstance()->createRedmine($param);
            if(!empty($red)&&isset($red['pwd'])){
                $sms.='redmine和svn密码为'.$red['pwd'].';';
                $check .='redmine和svn ';
            }else{
               $this->doLog($param,'add','redmineIT端创建失败');
            }

        }
        if(isset($param['mail_status'])&&$param['mail_status']==1){

            $mail = Itserver::getInstance()->createMail($param);


            if(!empty($mail) && isset($mail['passwd'])){
                //加入邮箱组
                $this->jointMailGroup($param);

                $sms.='mail密码为'.$mail['passwd'].';';
                $check .='mail ';
                $get =UserAccount::getInstance()->getAccount(
                    array(
                        'user_id'=>$param['user_id'],
                        'account_type'=>self::$TYPE_MAIL_PWD,
                    )
                );
                if(empty($get)){
                    $time  =time();
                    $time  = $time - $this->mail_minus;

                    $create_mail =UserAccount::getInstance()->createAccount(
                        array(
                            'user_id'=>$param['user_id'],
                            'account_type'=>self::$TYPE_MAIL_PWD,
                            'login_name'=>$param['mail'],
                            'status'=>1,
                            'update_time'=>date('Y-m-d H:i:s',$time)
                        )
                    );
                    if(empty($create_mail)){
                        //log

                        $this->doLog(    array(
                            'user_id'=>$param['user_id'],
                            'account_type'=>self::$TYPE_MAIL_PWD,
                            'login_name'=>$param['mail'],
                            'status'=>1,
                        ),'add','邮箱入库失败');
                    }
                }

            }else{
                //log
                $this->doLog( $param,'add','mailIT端创建失败');
            }

        }
        if(isset($param['computer_status'])&&$param['computer_status']==1){
            $sms .='电脑密码按hr给定为准初始为gaimima;';
            $check .='computer ';
        }
        return array(
            'sms'=>mb_substr($sms,0,-1,'utf-8'),
            'check'=>$check
        );
    }
    private function jointMailGroup($param=array()) {

        if(!isset($param['user_id'])){
          return false;
        }
        $user = UserInfo::getInstance()->getUserInfo(
            array(
                'user_id'=>$param['user_id']
            )
        );


        $user = is_array($user)?array_pop($user):'';
        //获取部门id
        $depart = Department::getInstance()->getDepart(
            array(
                'all'=>1,
                'status'=>1
            )
        );

        $d_r =  $this->findParent(isset($user['depart_id'])?$user['depart_id']:'',$depart,$user['depart_id']);
        unset($depart);

       if(!empty($d_r)){
           $d_r = MailGroupDepartRelation::getInstance()->getMailGroupDepartRelation(
               array(
                   'depart_id'=>$d_r,
               )
           );

       }
        $group_id_list = array_column(is_array($d_r)?$d_r:array(),'group_id');
        unset($d_r);

        is_array($group_id_list)&&$group_id_list= implode(',',$group_id_list);
        $group_name =array();
       if(!empty($group_id_list)){
           $group_name = MailGroup::getInstance()->getMailGroupList(
               array(
                   'group_id'=>$group_id_list
               )
           );

       }
        unset($group_id_list);
        $group_name = array_column(is_array($group_name)?$group_name:array(),'group_name');
        $group_name =  array_merge(is_array($group_name)?$group_name:array(),MailGroup::$com_mail_group);
        //掉第三方接口实现加入邮箱组
        $group_name = MailGroup::getInstance()->jointMailGroup(
            array(
                'u'=>implode(',',$group_name),
                'mail'=>isset($user['mail'])?$user['mail']:'',
            )
        );
        $this->doLog($group_name,'jointmailgroup','');
        return $group_name;
    }
    public function findParent($params,$data = array(),$t=null){

        if(isset($data[$params]['parent_id'])&&$data[$params]['parent_id']!=0){
            $t.=  ','.$data[ $data[$params]['parent_id']]['depart_id'];
            $t =  $this->findParent($data[$data[$params]['parent_id']]['depart_id'],$data,$t);
        }
        return $t = ltrim($t,',');
    }
    protected function doLog($new_param=array(),$old_param='add',$create_reason){
          
        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],'handle_id'=>100000,
            'operation_type'=>$old_param,'after_data'=>json_encode($new_param),'handle_type'=>self::$IT,
            'create_reason'=>$create_reason));
        return $ret;
    }


    private function _init() {
        $this->rules = array(//user_info
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',

            ),
            'vpn_status' => array(//1
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
            'wifi_status' => array(
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
            'redmine_status' => array(//2 3
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
            'mail_status' => array(//5
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
            'computer_status' => array(//电脑
                'type'=>'integer',
                'enum'=>array(0,1),
            ),

        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }
}