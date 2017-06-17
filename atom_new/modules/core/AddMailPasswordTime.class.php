<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserAccount;

/**
 * 增加第一次更改密码的时间
 * @author  haibinzhou@meilishuo.com
 * @since 2015-07-07
 */

class AddMailPasswordTime extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;

	public function run() {

		$this->_init();

        $result = UserAccount::model()->insert($this->params);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

    	$this->app->response->setBody($return);
	}

    /**
     * 参数初始化
     */
    protected function _init(){

        $this->rules = array(
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
            'account_type'  => array(
                'type'		=> 'integer',
            ),
            'login_name' 	=> array(
                'type'		=> 'string',
            ),
            'status' 	=> array(
                'type'		=> 'integer',
            ),
        );

        $this->params     = $this->post()->safe();

    }
}
