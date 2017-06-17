<?php
namespace Joint\Modules\Dayi_recruitment;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Utils\CryptAES;
use Joint\Package\Common\Response;
/**
 * 加密
 * @author hongzhou@meilishuo.com
 * @since 2015-8-25 下午1:53:13
 */
defined('SSO_HOST') || define("SSO_HOST", 'http://www.hotjob.cn/');
class AesUserInfo  extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
	private   $url    = 'wt/meilishuo/web/index/innerLogin!loginUser?';
    protected $secret_key = 'vMcJtALb4bKLHQMu';
	public function run() {

	$this->_init();

        if ($this->query()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }
        if(empty($this->params['date'])){
        	$this->params['date'] =date("YmdHi",time());//当前时间
        }
	//参数处理邮箱
 	    $is_mail = preg_match('/@/', $this->params['email']);
        if (!$is_mail && !empty($this->params['email'])) { // 邮箱
            $this->params['email'] .= '@meilishuo.com';
        }
        $this->params = array_filter($this->params);
        $this->params = json_encode($this->params);
        $result= $this->getEncrypt( $this->params ,$this->secret_key);
        if (empty($result)) {
            $return = Response::gen_error(30001,'','编码失败');
        }else{
            $result = SSO_HOST.$this->url.'brandCode=1&casFlag=Y&ticket='.$result;
            $return = Response::gen_success($result);
        }
        $this->app->response->setBody($return);
	
	}
	private function getEncrypt($sStr, $sKey) {

            $aes = new CryptAES();
            $aes->set_key($sKey);
            $aes->require_pkcs5();
            return $aes->encrypt($sStr);
	}
	private function _init() {

        $this->rules = array(
            'email' => array(
                'type'=>'string',
                'maxLength'=> 30
            ),
            'staffNum' => array(
                'type'=>'string',
                'maxLength'=> 30
            ),
            'realName' => array(
                'type'=>'string',
                'maxLength'=> 30
            ),
            'userName' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30
            ),
            'date' => array(
                'type'=>'string',
            )
            );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
        return TRUE;
    }
	
}
