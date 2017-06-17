<?php
namespace Admin\Modules\Structure\User;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * 一键注销
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class AjaxRecoverAllAuth extends BaseModule {
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
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        $delete_mail =$delete_vpn =$delete_red =$delete_svn=TRUE;
        $user_mail =$mail = array();
        //当前用户
        $time = @date('Y-m-d H:i:s',time());
        if(!isset($this->user['id'])){
            return $this->app->response->setBody(Response::gen_error(10002, '登录失败'));
        }
        //操作者
        $ret = UserInfo::getInstance()->getUserInfo(array('user_id'=>$this->user['id']));

        $ret = is_array($ret)?array_pop($ret):'';
        $user_mail = isset($ret['mail'])?$ret['mail']:'';
        //被操作者
        if(empty($this->params['user_id'])){
            return $this->app->response->setBody(Response::gen_error(10003, '删除用户不可为空'));
        }
        $mail = UserInfo::getInstance()->getUserInfo(
            array(
                'user_id'=>$this->params['user_id'],
                'limit'=>count($this->params['user_id']),
                'status'=>array(1,2,3)
            )
        );

        ///操作
        $info = null;
        foreach($mail as $key=>$val){

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
                $this->app->log->log('admin/structure/user/ajax_recover_all_auth_log', $del_user_ret);

                $get = $this->getId(   array(
                    'user_id'=>$this->params['user_id'],
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
                        'user_id'=>$this->params['user_id'],
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
//           $delete_red= $this->updateStatus(array('user_id'=>
//               $this->params['user_id'],'account_type'=>self::$TYPE_REDMINE,'status'=>0,'update_time'=>$time));
//            $delete_svn= $this->updateStatus(array('user_id'=>
//                $this->params['user_id'],'account_type'=>self::$TYPE_SVN,'status'=>0,'update_time'=>$time));
//            $delete_red = is_array($delete_red)?array_pop($delete_red):'';
//            $delete_svn = is_array($delete_svn)?array_pop($delete_svn):'';
            }
            if($this->params['wifi_status']==1){
                $delete = $this->deleteWifi(
                    array(
                        'mail'=>$mail,
                        'user_mail'=>$user_mail
                    )
                );
                if(!$delete){
                    $info .=$mail.'删除redmine&删除wifi失败';
                }
            }
            if(/*empty($delete_svn)||*/empty($delete_vpn)/*||empty($delete_red)*/||empty($delete_mail)){
                $info .=$mail.'录入数据库失败,通知speed管理员处理';
            }
            //log
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
    private function updateStatus($param) {
        $ret = self::getClient()->call('atom', 'core/update_mail_password_time', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }
    private function getId($param) {
        $ret = self::getClient()->call('atom', 'core/get_mail_password_time', $param);
        $ret = $this->parseApiData($ret);
        return $ret;
    }

    private function _init() {
        $this->rules = array(//user_info
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'multiId',
                'default'=>0
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
        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }
}