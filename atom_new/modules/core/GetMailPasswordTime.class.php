<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\MlsAccount;

/**
 * 获取用户修改密码的最后一次时间
 * @author haibinzhou@meilishuo.com
 * @since 2015-07-07
 */

class GetMailPasswordTime extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;

	
	public function run() {
		$this->_init();

        $result = MlsAccount::model()->getData($this->params);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
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
           'account_type' => array(
               'required' => TRUE,
               'allowEmpty' => TRUE,
               'type'=>'integer',
           ),
        );

        $this->params = $this->query()->safe();

        //$this->params['user_id'] = $this->app->currentUser['user_id'];
    }
}
