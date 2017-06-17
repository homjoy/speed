<?php
namespace Atom\Modules\A;

use \Libs\Util\Format;

class O extends \Frame\Module {

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
	    'bbbb' => 1234,
	    'cccc' => TRUE,
    );

    public function run() {
        $sql = "select * from t_dolphin_user_profile where user_id = 1227713";
        $result = BDB::getConn()->read($sql, array());

        if ($result) {
        	$return = array(
        		'code' => 200,
        		'data' => Format::outputData($result, self::$sample, TRUE),
        	);
        }else{
        	$return = array(
        		'code' => 500,
        		'error_message' => 'Error Result',
        		'data' => Format::outputData($result, self::$sample, TRUE),
        	);
        }

        $this->app->response->setBody($return);
        $this->app->log->log('testlogname', $return);
    }

}

class BDB extends \Libs\DB\DBConnManager {

    const _DATABASE_ = 'dolphin';

}
