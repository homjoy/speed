<?php
namespace Joint\Modules\Itserver;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
/**
 * GrantVpnAuth
 * @author hongzhou@meilishuo.com
 * @since 2015-9-23 下午4:53:13
 */
defined('VPN_WIFI_HOST') || define("VPN_WIFI_HOST", 'http://yz.it.api01.meiliworks.com/');
class GrantVpnAuth extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    private   $url    = 'apis/vpn-users.php?';
    private   $data   = NULL;
    const     seckey = 'Pmlsit2015vpn';
    public function run() {

        $this->_init();
        //参数处理
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }
        $is_mail = preg_match('/@/', $this->params['mail']);
        if ($is_mail) { // 邮箱
            $is_mail = explode("@",$this->params['mail']);
            $is_mail = $is_mail[0];;
        }else{
            $is_mail =  $this->params['mail'];
        }//得到前缀 拼接curl
        $is_user_mail = preg_match('/@/', $this->params['user_mail']);
        if ($is_user_mail) { // 邮箱
            $is_user_mail = explode("@",$this->params['user_mail']);
            $is_user_mail = $is_user_mail[0];
        }else{
            $is_user_mail =  $this->params['user_mail'];
        }
        $curl_time =time();
        //md
        $md_s  = self::seckey;
        $md_s .= $is_user_mail;
        $md_s .= $curl_time;
        $md_s  = md5($md_s);

        $this->data = array(
            'u'=> $is_mail,
            'op'    => $is_user_mail,
            'seckey'  => $md_s,
            'enabled'=>1,
            'act'  => 'inituser'
        );
        $this->url = VPN_WIFI_HOST.$this->url.http_build_query($this->data);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($this->url);
        $body = $ret['body'];
        $body = json_decode($body,TRUE);

        switch ($body['__STATUS__']) {
            case 'OK':
                $return = Response::gen_success(array('pwd'=>'Vpn@gaimima'));
                break;
            case 'ERROR':
                $return = Response::gen_error(30001,'', $body['__MSG__']);
                break;
            default:
                $return = Response::gen_error(50004,'','vpn权限授权失败');
                break;
        }

        $this->app->response->setBody($return);

    }


    private function _init() {

        $this->rules = array(
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'user_mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            )
        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }

}