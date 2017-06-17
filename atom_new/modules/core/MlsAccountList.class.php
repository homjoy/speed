<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\MlsAccount;

/**
 * 获取账号注册信息列表
 * @author minggeng@meilishuo.com
 * @since 2015-04-14
 */

class MlsAccountList extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
        $this->_init();
        $this->sample   = MlsAccount::model()->getFields();

        $queryParams = array();
        if (!empty($this->params['user_id'])) {
            if (strpos($this->params['user_id'], ',')) {
                $queryParams['user_id'] = explode(',', $this->params['user_id']);
            }else{
                $queryParams['user_id'] = intval($this->params['user_id']);
            }
        }
        if (!empty($this->params['account_type'])) {
            $queryParams['account_type'] = $this->params['account_type'];
        }
        if (empty($queryParams)) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }
        $result = MlsAccount::model()->getData($queryParams);

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
			'user_id'=>array(
				'type'=>'string',
			),
			'account_type'=>array(
				'type'=>'integer',
			),
		);
		$this->params   = $this->query()->safe();
	}

}
