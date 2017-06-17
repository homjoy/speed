<?php
namespace Apicloud\Modules\A;

use \Libs\Util\Format;
use \Libs\Serviceclient\Client;

class C extends \Frame\Module {

    private static $sample = array(
	    'user_id' => 0,
	    'nickname' => '',
	    'email' => '',
	    'ctime' => '',
	    'password' => '',
	    'active_code' => '',
	    'cookie' => '',
	    'is_actived' => 1,
	    'invite_code' => '',
	    'last_logindate' => '',
	    'status' => 1,
	    'realname' => '',
	    'istested' => 0,
	    'reg_from' => 10,
	    'last_email_time' => 0,
	    'level' => 0,
	    'isPrompt' => 0,
	    'isBusiness' => 0,
	    'login_times' => 0,
	    'is_mall' => 0,
	    'mall_url' => '',
	    'aaaa' => 'default',
	    'bbbb' => 1234.56,
	    'cccc' => TRUE,
    );

    public function run() {

        $param = array('url' => 'a/b');
        $paramData = array('id' => '123');
        \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 0, 'ip' => '127.0.0.1'));

        $clientObj = new Client();
        $result = $clientObj->call('atom', $param['url'], $paramData);
        if ($result['httpcode'] == 200 && isset($result['content']) && $result['content']['code'] == 200) {
            $data = $result['content']['data'];
        	$return = array(
        		'code' => 200,
        		'error_code' => 0,
        		'data' => Format::outputData($data, self::$sample, TRUE),
        	);
        }else{
        	$return = array(
        		'code' => 200,
        		'error_code' => 10001,
        		'error_message' => 'Internal server error',
        	);

        }

        $this->app->response->setBody($return);
        $this->app->log->log('testlogname', $return);
    }

}
