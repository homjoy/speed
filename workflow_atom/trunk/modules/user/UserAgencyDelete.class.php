<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserAgency;
use Libs\Util\Format;

/**
 * 逻辑删除用户代理
 * @author yixiangwang@meilishuo.com
 * @date 2015-10-13
 */

class UserAgencyDelete extends BaseModule {

    protected $params;
	
    public function run() {

    	if( !$this->_init() ) {
			return FALSE;
		}

        $result = UserAgency::getInstance()->logicDelete($this->params['aid']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($result)) {
            $return = Response::gen_error(50004);
        } else {
   	        $return = Response::gen_success(array('msg' => '修改成功'));
        }

        $this->app->response->setBody($return);
    }

    private function _init() {
		$this->params['aid'] = isset($this->request->POST['aid']) ? $this->request->POST['aid'] : array();
		
		if( empty($this->params['aid']) ){
        	$this->app->response->setBody(Response::gen_error(10001, '参数错误'));
			return FALSE;
		}
		
		return TRUE;
    }
}