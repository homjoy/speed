<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 查询用户代理关系
 * @author yixiangwang@meilishuo.com
 * @since 2015-10-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class UserAgencySet extends BaseModule {
	
	protected $checkUserPermission = TRUE;
	private $params;

	public function run() {
		$this->_init();
		
		//参数校验
		if($this->query()->hasError()){
			$return = Response::gen_error(10001, '', $this->query()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		$userInfo = $return = array();
		$user_name = '';
		$agencyInfo = array();
		
		if( !empty($this->params['data']) ) {
			
			$userInfo = explode('{|{}}', $this->params['data']);
			if(empty($userInfo)) {
				$return = Response::gen_error(10001, '查询参数错误');
				return $this->app->response->setBody($return);
			}
			
			//获取部门关系 & 代理信息
			$multiClient = $this->getMultiClient();
			$multiClient->call('workflowatom', 'user/user_agency_list', array(
				'o_uid'	=> $userInfo[0]
			), 'agencyInfo');
			$multiClient->call('atom', 'department/get_depart_relation', array(
				'mail'	=> $userInfo[2]
			), 'relationInfo');
			$multiClient->callData();
			
			$error_msg = '';
			if(!empty($multiClient->agencyInfo['content']['error_msg'])) {
				$error_msg .= 'agencyInfo：' . $multiClient->agencyInfo['content']['error_msg'] . '<br />';
			}
			if(!empty($multiClient->relationInfo['content']['error_msg'])) {
				$error_msg .= 'agencyInfo：' . $multiClient->relationInfo['content']['error_msg'] . '<br />';
			}
			if(!empty($error_msg)) {
				return $this->app->response->setBody(Response::gen_error(10000, $error_msg));
			}
			
			$agencyInfo = $this->parseApiData($multiClient->agencyInfo);
			$relationInfo = $this->parseApiData($multiClient->relationInfo);
			
			if(!empty($agencyInfo)) {
				
				$userIds = array();
				$deptIds = array();
				
				foreach($agencyInfo as $v) {
					$deptIds[] = $v['o_depart_id'];
					$v['a_uid'] = explode(',', $v['a_uid']);
					$userIds = $userIds + $v['a_uid'];
				}
				
				//获取用户，部门数据
				$multiClient = $this->getMultiClient();
				$multiClient->call('atom', 'account/get_user_info', array(
					'user_id'	=> $userIds
				), 'userInfo');
				$multiClient->call('atom', 'department/depart_info_list', array(
					'depart_id'	=> implode(',', $deptIds)
				), 'deptInfo');
				$multiClient->callData();
				
				$error_msg = '';
				
				if(!empty($multiClient->userInfo['content']['error_msg'])) {
					$error_msg .= $multiClient->userInfo['content']['error_msg'] . '<br />';
				}
				
				if(!empty($multiClient->deptInfo['content']['error_msg'])) {
					$error_msg .= $multiClient->deptInfo['content']['error_msg'] . '<br />';
				}
				
				if(!empty($error_msg)) {
					return $this->app->response->setBody(Response::gen_error(10000, $error_msg));
				}
				
				$userInfo = $this->parseApiData($multiClient->userInfo);
				$deptInfo = $this->parseApiData($multiClient->deptInfo);		
			}
		}
		
		$this->app->response->setBody(array(
			'data'				=> $return,
			'user_name'			=> $user_name,
			'searchUserInfo'	=> $userInfo,
			'agencyInfo'		=> $agencyInfo
		));
	}
	
	private function _init() {
		$this->rules = array(
			'data' 	=> array(
				'type'       => 'string',
			),
		);
		
		$this->params = $this->query()->safe();
	}
}