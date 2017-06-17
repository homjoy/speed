<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserAgency;
use Libs\Util\Format;

/**
 * 添加用户代理
 * @author yixiangwang@meilishuo.com
 * @date 2015-10-16 
 */

class UserAgencyCreate extends BaseModule {

	protected $params = array();
	private $sample;

	public function run() {

		if( !$this->_init() ) {
			return FALSE;
		}
		
		foreach($this->params['data'] as $k => $v) {
			if(empty($v['o_uid']) || empty($v['o_depart_id']) || empty($v['a_uid'])) {
				unset($this->params['data'][$k]);
			}
		}
		
        if (empty($this->params['data'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }

		$insertIds = array();
		foreach($this->params['data'] as $insertData) {
			$insertIds[] = $result = UserAgency::getInstance()->insert($insertData);
			
			if($result === FALSE || empty($result)) {
				break;
			}
		}
		
		if ($result === FALSE) {
			$return =  Response::gen_error(50001);
		} else if (empty($result)) {
			$return = Response::gen_error(50003);
		} else {
			$return = Response::gen_success($insertIds);
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