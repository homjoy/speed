<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserAgency;
use Libs\Util\Format;

/**
 * 用户代理信息列表
 * @author yixiangwang@meilishuo.com
 * @date 2015-10-13
 */

class UserAgencyList extends BaseModule {

	protected $params;
	private $sample;

	public function run() {

		$this->_init();

		$this->sample = UserAgency::getInstance()->getFields();

		if($this->post()->hasError()){
        	$return = Response::gen_error(10001, '', $this->post()->hasError());
        	return $this->app->response->setBody($return);
        }

        $queryParams = array();
		if (!empty($this->params['aid'])) {
        	$queryParams['aid'] = $this->params['aid'];
        }
        if (!empty($this->params['o_uid'])) {
        	$queryParams['o_uid'] = $this->params['o_uid'];
        }
        if (!empty($this->params['o_depart_id'])) {
        	$queryParams['o_depart_id'] = $this->params['o_depart_id'];
        }
        $queryParams['status'] = explode(',', $this->params['status']);
        
        $result = UserAgency::getInstance()->getDataList($queryParams);
		
		if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result) && !is_array($result)) {
        	$return = Response::gen_error(50002);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }

        $this->app->response->setBody($return);
	}

	private function _init() {

		$this->rules = array(
			'aid'  => array(
				'type' => 'integer', 
			),
			'o_uid' => array(
				'type' => 'integer', 
			),
			'o_depart_id' => array(
				'type' => 'integer', 
			),
			'status'  => array(
				'type'    => 'string',
				'default' => '1',
			),
		);

		$this->params = $this->post()->safe();
	}
}