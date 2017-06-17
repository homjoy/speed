<?php
namespace Joint\Modules\Itserver;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
/**
 * GrantRedmineAuth
 * @author hongzhou@meilishuo.com
 * @since 2015-9-23 下午4:53:13
 */
defined('REDMINE_HOST') || define("REDMINE_HOST", 'http://redmine.meilishuo.com/');
class GrantRedmineAuth extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    private   $url    = 'users.json';
    const     password = 'RedSvn@gaimima';

    public function run() {
        $this->_init();
        //参数处理
        $post_fields = array();
        $is_mail = preg_match('/@/', $this->params['mail']);
        if (!$is_mail) { // 邮箱
            $is_mail = $this->params['mail'];
            $is_mail .= '@meilishuo.com';
        }else{
            $is_mail =  $this->params['mail'];
        }
        $post_fields['mail'] = $is_mail;


        if(empty($this->params['name_cn'])){//firstname  lastname 获取方式
            $post_fields['firstname'] = strtok($is_mail,'@');
            $post_fields['lastname']  = $post_fields['firstname'];
        }else{
            $post_fields['firstname'] = mb_substr($this->params['name_cn'],0,-1,'utf-8');
            $post_fields['lastname']  = mb_substr($this->params['name_cn'],0,1,'utf-8');
        }

        $post_fields['must_change_passwd'] = TRUE;
        $post_fields['password'] = self::password;
        $post_fields['login']  =  strtok($is_mail,'@');

        $headers[] = "X-Redmine-API-Key:4a6aaffedda1d5d2a4a5bdf28f30362de1bff4f7";
        $headers[] = "Content-type: application/json";

        $method = 'POST';

        $url = REDMINE_HOST;
        $url .=$this->url;
        $fields['user'] = $post_fields;
        $fields = json_encode($fields, TRUE);

        $ret = $this->http($url,$method,$fields, $headers);
        $ret = json_decode($ret, TRUE);

        if (isset($ret['errors']) &&  $ret['errors']!='') {
            $return =  Response::gen_error(50001,'',$ret['errors']);;
        }elseif (isset($ret['user']) &&  $ret['user']!='') {
            $return = Response::gen_success(array('pwd'=>self::password));
        }else{
            $return = Response::gen_error(30001,'','其它错误');
        }
        $this->app->response->setBody($return);

    }

    private function http($url=array(), $method=array(), $post_fields = array(), $headers = array()) {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_POST, TRUE);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        if (!empty($post_fields)) {
            curl_setopt($ci, CURLOPT_POSTFIELDS, $post_fields);
        }
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ci, CURLOPT_TIMEOUT, 500);
        $response = curl_exec($ci);
        curl_close ($ci);
        return $response;
    }
    private function _init() {

        $this->rules = array(
            'mail' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'name_cn' => array(
                'type'=>'string',
                'maxLength'=> 30
            ),
        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
        return TRUE;
    }

}