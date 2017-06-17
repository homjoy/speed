<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserAgency;
use Libs\Util\Format;

/**
 * 更新用户代理数据
 * @author yixiangwang@meilishuo.com
 * @date 2015-10-16
 */

class UserAgencyUpdate extends BaseModule {

	protected $params = array();
	private $sample;

	public function run() {
		
		if( !$this->_init() ) {
			return FALSE;
		}
		
		foreach($this->params['data'] as $k => $v) {
			if( empty($v['aid']) ) {
				unset($this->params['data'][$k]);
			}
		}
		
        if ( empty($this->params['data']) ) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }
		
		foreach($this->params['data'] as $updateItem) {
			$result = UserAgency::getInstance()->update($updateItem);
			
			if ($result === FALSE || empty($result)) {
				break;
			}
		}
		
		if ($result === FALSE) {
			$return =  Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50004);
		} else {
			$return = Response::gen_success($return);
		}

		$this->app->response->setBody($return);
	}

	/**
     * 参数初始化
     */
	private function _init() {
		
		$this->params['data'] = isset($this->request->POST['data']) ? $this->request->POST['data'] : array();
		
		if( empty($this->params['data']) ){
        	$this->app->response->setBody(Response::gen_error(10001, '参数错误'));
			return FALSE;
		}

		return TRUE;
	}
}