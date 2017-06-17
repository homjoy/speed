<?php
namespace Joint\Modules\Releaseauth;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
/**
 * mail权限回收
 * @author hongzhou@meilishuo.com
 * @since 2015-11-5 下午4:53:13
 */
defined('MAIl_HOST') || define("MAIl_HOST", 'http://yz.it.api01.meiliworks.com/');
class MailAuthRecover extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    private   $url    = 'apis/mail/core_api_v1.php?';
    private   $delete = 'deleteUser';
    private   $exist = 'userExist';
    public function run() {

        $this->_init();
        //参数处理
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }
        //检验是否存在
        //不存在
        $is_mail = preg_match('/@/', $this->params['mail']);
        if (!$is_mail) { // 邮箱
            $this->params['mail'] .='@meilishuo.com';
        }
        $isExist = $this->isExist(array(
            'u'=> $this->params['mail'],
            'seckey'  => $this->checkKey($this->exist),
            'act'  => $this->exist
        ));

        if($isExist){
            return $this->app->response->setBody(Response::gen_success(array('msg'=>' 用户不存在')));
        }
        //存在则删除
        $this->data = array(
            'u'=> $this->params['mail'],
            'seckey'  => $this->checkKey($this->delete),
            'act'  => $this->delete,
        );
        $url= MAIl_HOST.$this->url.http_build_query($this->data);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($url);
        $body = $ret['body'];
        $body = json_decode($body,TRUE);

        switch ($body['__STATUS__']) {
            case 'OK':
                $return = Response::gen_success($body['__MSG__']);
                break;
            case 'ERROR':
                $return = Response::gen_error(30001,'', $body['__MSG__']);
                break;
            default:
                $return = Response::gen_error(50004,'','权限收回失败');
                break;
        }

        $this->app->response->setBody($return);

    }
    private function isExist($params =array()){
        $url = MAIl_HOST.$this->url.http_build_query($params);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($url);
        $body = $ret['body'];
        $body = json_decode($body,TRUE);
        if ($body['__STATUS__']=='OK') {
            return TRUE;
        }
        return false;
    }

    private  function checkKey($act){
        $ts = time();
        $sharedkey = "CoreMail2015";
        $nseckey = $sharedkey . $act . "$ts" ;
        $seckey = md5($nseckey);
        return $seckey;
    }
    private function _init() {

        $this->rules = array(
            'mail' => array(
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