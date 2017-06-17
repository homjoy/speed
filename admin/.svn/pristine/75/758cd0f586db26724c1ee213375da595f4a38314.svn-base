<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 获取用户部门关系相关信息
 * @author yixiangwang@meilishuo.com
 * @since 2015-10-12
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class AjaxSearchUserDepartRelation extends BaseModule {
	
	private $params;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		$this->_init();
		
		//参数校验
		if($this->post()->hasError()){
			$return = Response::gen_error(10001, '', $this->post()->getErrors());
			return $this->app->response->setBody($return);
		}
		
		if(empty($this->params['data'])) {
			$return = Response::gen_error(10001, '参数错误');
			return $this->app->response->setBody($return);
		}
		
		$this->params['data'] = explode('{|{}}', $this->params['data']);
		
		$relationInfo = $this->getClient()->call('atom', 'department/get_depart_relation', array(
			'mail'	=> $this->params['data'][2]
		));
		$relationInfo = $this->parseApiData($relationInfo);
		
		if(FALSE === $relationInfo) {
			return FALSE;
		}
		
		$roleIds = array();
		$deptIds = array();
		
		if(!empty($relationInfo)) {
			foreach($relationInfo as $v) {
				$roleIds[] = $v['role_id'];
				$deptIds[] = $v['depart_id'];
			}
		
			//获取role数据，部门数据
			$multiClient = $this->getMultiClient();
			$multiClient->call('atom', 'account/user_job_role_list', array(
				'role_id'	=> $roleIds
			), 'roleInfo');
			$multiClient->call('atom', 'department/depart_info_list', array(
				'depart_id'	=> implode(',', $deptIds)
			), 'deptInfo');
			$multiClient->call('workflowatom', 'user/user_agency_list', array(
				'o_uid'		=> $this->params['data'][0],
				'status'	=> '0,1'
			), 'agencyInfo');
			$multiClient->callData();
			
			$error_msg = '';
			
			if(!empty($multiClient->roleInfo['content']['error_msg'])) {
				$error_msg .= $multiClient->roleInfo['content']['error_msg'] . '<br />';
			}
			
			if(!empty($multiClient->deptInfo['content']['error_msg'])) {
				$error_msg .= $multiClient->deptInfo['content']['error_msg'] . '<br />';
			}
			
			if(!empty($multiClient->agencyInfo['content']['error_msg'])) {
				$error_msg .= $multiClient->agencyInfo['content']['error_msg'] . '<br />';
			}
			
			if(!empty($error_msg)) {
				return $this->app->response->setBody(Response::gen_error(10000, $error_msg));
			}
			
			$roleInfo = $this->parseApiData($multiClient->roleInfo);
			$deptInfo = $this->parseApiData($multiClient->deptInfo);
			$agencyInfoTmp = $this->parseApiData($multiClient->agencyInfo);
		
			$agencyInfo = array();
			$userIds = array();
			if (!empty($agencyInfoTmp)) {
				foreach($agencyInfoTmp as $v) {
					$agencyInfo[$v['o_depart_id']] = $v;
					$userIds[] = $v['a_uid'];
				}
			}

			foreach ($relationInfo as $k => $v) {
				if ($k != $v['depart_id']) {
					$relationInfo[$v['depart_id']] = $v;
					unset($relationInfo[$k]);
				}	
			}
			
			if (!empty($userIds)) {
				$userInfo = $this->getClient()->call('atom', 'account/get_user_info', array(
					'user_id' => implode(',', $userIds),
					'all' => 1
				));
				$userInfo = $this->parseApiData($userInfo);
				if(FALSE === $userInfo) {
					return FALSE;
				}
			}
		
			foreach($relationInfo as $k => $v) {
				$relationInfo[$k]['role_name'] = $roleInfo[$v['role_id']]['role_name'];
				$relationInfo[$k]['dept_name'] = $deptInfo[$v['depart_id']]['depart_name'];
				if( !empty($agencyInfo[$v['depart_id']]) ) {
					if ($agencyInfo[$v['depart_id']]['a_uid'] == 'auto') {
						$relationInfo[$k]['agency_info_string'] = $agencyInfo[$v['depart_id']]['a_uid'];
						$relationInfo[$k]['agency_info_status'] = $agencyInfo[$v['depart_id']]['status'];
						$relationInfo[$k]['agency_info'][] = array(
							'uid'       => 'auto',
							'user_name' => '机器自动跳过',
						);
					} else {
						$uids = explode(',', $agencyInfo[$v['depart_id']]['a_uid']);
						$relationInfo[$k]['agency_info_string'] = $agencyInfo[$v['depart_id']]['a_uid'];
						$relationInfo[$k]['agency_info_status'] = $agencyInfo[$v['depart_id']]['status'];
						foreach($uids as $uid) {
							$relationInfo[$k]['agency_info'][] = array(
								'uid'		=> $uid,
								'user_name'	=> $userInfo[$uid]['name_cn']
							);
						}
					}
				}
			}
		}
		
		$this->app->response->setBody(Response::gen_success(array(
			'data'			=> $this->params['data'],
			'relationInfo'	=> $relationInfo
		)));
	}
	
	private function _init() {
		$this->rules = array(
			'data' 	=> array(
				'type'       => 'string',
			),
		);
		
		$this->params = $this->post()->safe();
	}
}