<?php
namespace Joint\Modules\Releaseauth;
use Libs\Util\Format;
use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
/**
 * 报警短信解除
 * @author hongzhou@meilishuo.com
 * @since 2015-8-18 下午1:53:13
 */
defined('AMR_HOST') || define("AMR_HOST", 'http://smsapi.meilishuo.com/');
class AlterMsgRemove  extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
	private   $url    = 'smssys/interface/putOutFlag.php?';
	
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
	        $is_mail = $is_mail[0];
	    }else{
	    	$is_mail =  $this->params['mail'];
	    }//得到前缀 拼接curl
	    $this->url = AMR_HOST.$this->url.http_build_query(array('login' =>$is_mail));
		$curl_obj = new \Libs\Sphinx\curl;
		$ret = $curl_obj->get($this->url);

  		if (!isset($ret['body']) ) {//没有说明 没传过去
        	$return = Response::gen_error(10001,'','参数未传过去');
        }elseif (empty($ret['body'])) {//实际上不是空是0 
        	$return = Response::gen_error(50001,'','标记失败');
        }else{
        	$return = Response::gen_success(array('message'=>$ret['body']));
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
			)
			);

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
		return TRUE;
	}
	
}