<?php
namespace Admin\Modules\Itserver;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * 一键注销
 * @author hongzhou@meilishuo.com
 * @since 2015-02-24 下午12:53:13
 */
class AjaxRecoverAllAuthByMail extends BaseModule {
    private $params = array();
    protected $errors = array();
    public static $TYPE_VPN = 1;
    public static $TYPE_REDMINE = 2;
    public static $TYPE_SVN = 3;
    public static $TYPE_OSYS = 4;
    public static $TYPE_MAIL_PWD = 5; //邮箱密码修改登记
    public static $TYPE_WIFI= 6;
    public static $VIEW_SWITCH_JSON = TRUE;

    public static $IT = 8;
    public function run() {
        $this->_init();

        //校验前台传过字段
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '用户名不可为空');
            return $this->app->response->setBody($return);
        }

        $delete_mail = $delete_vpn =$delete_red =$delete_svn=TRUE;
        $user_mail = $mail = array();
        //当前用户
        $time = date('Y-m-d H:i:s',time());
        if(!isset($this->user['id'])){
            return $this->app->response->setBody(Response::gen_error(10002, '登录失败'));
        }
        //操作者
        $ret = UserInfo::getInstance()->getUserInfo(array('user_id'=>$this->user['id']));

        $ret = is_array($ret)?array_pop($ret):'';
        $user_mail = isset($ret['mail'])?$ret['mail']:'';
        if(empty($this->params['mail'])){
            return $this->app->response->setBody(Response::gen_error(10003,'用户名不可以为空'));
        }
        //被操作者
        $mail_array =array();
        $mail_list = str_replace(array("\r\n", "\r", "\n", " ", " "), "#", $this->params['mail']);
        $mail_list = explode("#", $mail_list);
        $mail_list = array_unique($mail_list);
        foreach ($mail_list as $mail_key => &$mail_list_value) {
            $mail_list_value = trim($mail_list_value);
            if (empty($mail_list_value)) {
                unset($mail_list[$mail_key]);
            }else{
                if(preg_match('/@/', $mail_list_value)){
                    $mail_list_value = strtok($mail_list_value,'@');
                }
            }
        }
        if(empty($mail_list)){
            return $this->app->response->setBody(Response::gen_error(10003,'无可删除用户'));
        }

//底层不兼容多mail 这里先不处理
        foreach( $mail_list as &$val){
            $temp_mail = UserInfo::getInstance()->getUserInfo(
                array(
                    'mail'=>$val,
                    'limit'=>1,
                    'status'=>array(1,2,3)
                )
            );
            $mail_array[] =is_array($temp_mail)?array_pop($temp_mail):'';
        }

        $mail_array = array_filter($mail_array);
        if(empty($mail_array)){
            return $this->app->response->setBody(Response::gen_error(10003, '删除用户不是speed用户请走独立删除'));
        }
        
        ///操作
        $info = null;
        foreach($mail_array as &$val){
            $mail = isset($val['mail'])?$val['mail']:'';

            if($this->params['mail_status']==1){
                $delete = $this->deleteMail(array('mail'=>$mail));
                if(!$delete){
                    $info .=$mail.'删除mail失败';
                }
                //从邮件组中删除

                $del_user_ret = $this->delUserFromUserGroup(

                    isset($val['user_id'])?$val['user_id']:''

                );
                $del_user_ret['user_id'] =$val['user_id'];
                if(isset($del_user_ret['code'])==200){
                    $del_user_ret['data'] = '邮箱从邮件组删除成功';
                }
                $this->doLog($del_user_ret);

                $get = $this->getId(   array(
                    'user_id'=>isset($val['user_id'])?$val['user_id']:'',
                    'account_type'=>self::$TYPE_MAIL_PWD,
                ));
                $get =is_array($get)?array_pop($get):'';
                if($get){
                    $delete_mail=$this->updateStatus(
                        array(
                            'id'=>isset($get['id'])?$get['id']:'',
                            'status'=>0,
                            'update_time'=>$time
                        )
                    );
                }
            }
            if($this->params['vpn_status']==1){
                $delete = $this->deleteVpn(
                    array(
                        'mail'=>$mail,
                        'user_mail'=>$user_mail
                    )
                );
                if(!$delete){
                    $info .=$mail.'删除vpn失败';
                }
                $get = $this->getId(
                    array(
                        'user_id'=>isset($val['user_id'])?$val['user_id']:0,
                        'account_type'=>self::$TYPE_VPN,
                    )
                );
                $get =is_array($get)?array_pop($get):'';
                if($get){
                    $delete_vpn=$this->updateStatus(
                        array(
                            'id'=>isset($get['id'])?$get['id']:'',
                            'status'=>0,
                            'update_time'=>$time
                        )
                    );
                }
            }
            if($this->params['redmine_status']==1){
                $delete = $this->deleteRedmine(
                    array(
                        'mail'=>$mail,
                        'user_mail'=>$user_mail
                    )
                );
                if(!$delete){
                    $info .=$mail.'删除redmine&svn失败';
                }
                $delete_red =$delete_svn =FALSE;

            }
            if($this->params['wifi_status']==1){
                $delete = $this->deleteWifi(
                    array(
                        'mail'=>$mail,
                        'user_mail'=>$user_mail
                    )
                );
                if(!$delete){
                    $info .=$mail.'删除wifi失败';
                }
            }
            if(empty($delete_vpn)||empty($delete_mail)){
                $info .=$mail.'录入数据库失败,通知speed管理员处理';
            }

            if($this->params['yun_status']==1){
                $delete = $this->deleteYun(
                    array(
                        'mail'=>$mail,
                    )
                );
                if(!$delete){
                    $info .=$mail.'删除yun账号失败';
                }
            }
            $this->params['info'] = $info;
            $this->doLog($this->params);
        }
        if(!empty($info)){
            return $this->app->response->setBody(Response::gen_error(50001,$info));
        }

        $this->app->response->setBody(Response::gen_success('删除成功'));

    }
    protected function doLog($new_param=array(),$old_param='delete'){

        $ret = Log::getInstance()->createLogs(array(
                'user_id'=>$this->user['id'],
                'handle_id'=>100000,
                'operation_type'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>self::$IT
            )
        );
        return $ret;
    }

    /**
     * 从邮件组删除 用户
     * @param $mail
     */
    protected function delUserFromUserGroup($user_id){
        if(empty($user_id)) {
            return array();
        }
        $params = array(
            'user_id' => $user_id,
            'status'  => 0
        );
        $ret = self::getClient()->call('atom', 'mail/mail_group_user_update', $params);
        $ret = $this->parseApiData($ret);
        return $ret;
    }

    private function deleteVpn($param) {
        $ret = self::getClient()->call('joint', 'releaseauth/vpn_auth_recover', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function deleteRedmine($param) {
        $ret = self::getClient()->call('joint', 'releaseauth/redmine_auth_recover', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function deleteMail($param) {
        $ret = self::getClient()->call('joint', 'releaseauth/mail_auth_recover', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function deleteWifi($param) {
        $ret = self::getClient()->call('joint', 'releaseauth/wifi_auth_recover', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function deleteYun($param) {
        $ret = self::getClient()->call('atom', 'releaseauth/yun_auth_recover', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function updateStatus($param) {
        $ret = self::getClient()->call('atom', 'core/update_mail_password_time', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }

    /**
     * 获取过期表中数据 vpn mail
     * @param $mail
     */
    private function getId($param) {
        $ret = self::getClient()->call('atom', 'core/get_mail_password_time', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }

    private function _init() {
        $this->rules = array(//user_info
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
            ),
            'vpn_status' => array(//1
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
            'wifi_status' => array(
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
            'yun_status' => array(
                'type'=>'integer',
                'enum'=>array(0,1),
            ),
        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }
}