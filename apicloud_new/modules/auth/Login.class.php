<?php

namespace Apicloud\Modules\Auth;

use Libs\Serviceclient\Client;

use Apicloud\Package\Common\Response;
use Libs\Cache\Memcache;
use Libs\Util\Utilities;

use Apicloud\Modules\Common\BaseModule;
use Frame\Speed\Exception\ParameterException;
use Apicloud\Package\Core\Config;
//调用的登陆方式
use Apicloud\Package\Auth\MlsLogin;
use Apicloud\Package\Auth\MgjLogin;
use \Libs\Sphinx\curl;

/**
 * 新版登陆接口 (美丽说,蘑菇街...)
 * Class Login
 * @package Apicloud\Modules\Auth
 * @date 2016-03-26
 */

class Login extends BaseModule
{

    protected $params = array();
    protected $errors = array();
    private $user = NULL;

    private $mem_mark_key = NULL;
    private $account = NULL;

    private $token_expire = 604800; //7天 cookie & memcache存储时间
    private $mfa_code_expire = 604800; //7天 cookie & memcache存储时间
    //BBS 同步登陆接口

    public function run()
    {
        $this->_init();
        switch ($this->params['suffix']) {
            case 1:
                $this->user = MlsLogin::getInstance()->login($this->params);
                break;
            case 2:
                $this->user = MgjLogin::getInstance()->login($this->params);
                break;
            default:
                throw new ParameterException('调用登陆方法失败');
        }
        //存储数据到MEMCACHE
        $this->marked($this->user);
        $this->markLogin($this->user);

        $return = array(
            'user_id' => $this->user['user_id'],
            'speed_token' => $this->mem_mark_key,
            'token_expire' => $this->token_expire,
            'user' => $this->user,
        );
        $this->_syncBbsLogin($this->user,$this->params);
        $this->app->response->setBody(Response::gen_success($return));
        $this->app->log->log('loginMsg', $return);
    }

    /**
     * 登陆成功后,post 数据到bbs
     */
    private function _syncBbsLogin($user_info,$params){
        if(empty($user_info) || empty($params)){
            //登陆记录失败
            $this->app->log->log('bbs_sync_login_error', 'userinfo为空');
            return FALSE;
        }

        $post_params = array(
            'email' => $user_info['mail'],
            'name'  => $user_info['name_nick'],
            'pwd'   => $params['password'],
            'avatar_url' => array(
                'big' => $user_info['avatar'],
                'middle' =>  $user_info['avatar'],
                'small' => $user_info['avatar']
            ),


        );
        $post_params = http_build_query($post_params);
        $curl_obj = new curl();
        $return_info = $curl_obj->post(BBS_URL.'/bbs/sync/bbs_sync_login.php', $post_params);
        $return_info_body = isset($return_info['body']) ? $return_info['body'] : '';
        if(empty($return_info_body)){
            $this->app->log->log('bbs_sync_login_error', '返回值为空');
            return FALSE;
        }

        $return_info_array = json_decode($return_info_body,true);

        if(empty($return_info_array['code']) || $return_info_array['code'] !=200 ){
            $this->app->log->log('bbs_sync_login_error', $return_info_body);
            return FALSE;
        }else{
            if(!empty( $return_info_array['cookie_data']) && is_array($return_info_array['cookie_data'])){
                foreach( $return_info_array['cookie_data'] as $cookie_key => $cookie_value){
                    setcookie($cookie_key,$cookie_value['value'],$cookie_value['expires'],'/');
                }
            }else{
                $this->app->log->log('bbs_sync_login_error', 'cookie写入失败');
                return FALSE;
            }
        }

    }
    //设置缓存
    private function marked($user)
    {
        $this->mem_mark_key = Utilities::getUniqueId();
        Memcache::instance()->set($this->mem_mark_key, $user, $this->token_expire);
        Memcache::instance()->set($this->mem_mark_key . 'mfa_code', $user, $this->mfa_code_expire);
    }

    //登陆记录
    private function markLogin($user)
    {
        $login = array(
            'user_id' => $user['user_id'],
            'ip' => ip2long($_SERVER['REMOTE_ADDR']),
            'salt' => '',
            'password' => md5($this->params['password']),
            'session' => $this->mem_mark_key,
            'expire_time' => isset($this->account['expire']) ? $this->account['expire'] : 0,
        );

        $clientObj = new Client();
        $ret = $clientObj->call('atom', 'user/login_info_create', $login);
        if ($ret['httpcode'] == 200 && !empty($ret['content']) && $ret['content']['code'] == 200) {
            return TRUE;
        }

        //登陆记录失败
        $this->app->log->log('login_info_error', $login);
        return FALSE;
    }


    private function _init()
    {
        $this->rules = array(
            'username' => array(
                'required' => true,
                'type' => 'string',
            ),
            'password' => array(
                'required' => true,
                'type' => 'string',
            ),

            'suffix' => array(
                'required' => true,
                'type' => 'string', // 1 meilishuo.com  2 mogujie.com  3 taoshijie.com
            ),
        );
        $this->params = $this->post()->safe();
        //密码过期时间
        $p = array(
            'path' => '/common/login/password_expire',
        );
        $password_expire = Config::getInstance()->getValue($p);//一年
        $this->token_expire = $password_expire * 86400;

        $p = array(
            'path' => '/common/login/mfa_code_expire',
        );
        $mfa_code_expire = Config::getInstance()->getValue($p);//一周
        $this->mfa_code_expire = $mfa_code_expire * 86400;

        $this->errors = $this->post()->getErrors();
        //邮箱后缀
        $p = array(
            'path' => '/common/mail/suffix/' . $this->params['suffix'],
        );
        $mail_suffix = Config::getInstance()->getValue($p);//admin
        if (empty($mail_suffix)) {
            throw new ParameterException('邮箱后缀不存在');
        }
        $this->params['mail_suffix'] = $mail_suffix;
        $this->params['ip'] = $this->app->request->realIp();
    }

}
