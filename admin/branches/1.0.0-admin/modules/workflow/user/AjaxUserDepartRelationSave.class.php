<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 保存代理信息
 * @author yixiangwang@meilishuo.com
 * @since 2015-10-12
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class AjaxUserDepartRelationSave extends BaseModule {
	
	private $params;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		
		if( !$this->_init() ) {
			return FALSE;
		}
		
		$deleteAgencyIds = $updateAgencyInfo = $createAgencyInfo = array();
		
		//读取原始代理数据
		$userAgencyInfoOrigin = $this->getClient()->call('workflowatom', 'user/user_agency_list', array(
			'o_uid'		=> $this->params['o_uid'],
			'status'	=> '0,1'
		));
		$userAgencyInfoOrigin = $this->parseApiData($userAgencyInfoOrigin);
		if(FALSE === $userAgencyInfoOrigin) {
			return FALSE;
		}
		
		//格式化代理数据
		$userAgencyInfo = array();
		foreach($userAgencyInfoOrigin as $v) {
			$userAgencyInfo[$v['o_depart_id']] = $v;
			if( !in_array($v['o_depart_id'], $this->params['depart_id']) ) {
				//要删除的aid
				$deleteAgencyIds[] = $v['aid'];
			}
		}
		
		//需要更新的数据
		if( !empty($userAgencyInfo) ) {
			foreach( $userAgencyInfo as $deptId => $agencyInfoItem ) {
				if( !empty($this->params['data']['str_' . $deptId]) && ($agencyInfoItem['a_uid'] != $this->params['data']['str_' . $deptId] || empty($this->params['depart_id'][$deptId]) != $agencyInfoItem['status'])) {
					$updateAgencyInfo[] = array(
						'aid'		=> $agencyInfoItem['aid'],
						'a_uid'		=> $this->params['data']['str_' . $deptId],
						'status'	=> 1
					);
				}
				unset($this->params['data']['str_' . $deptId], $this->params['data'][$deptId]);
			}
		}
		
		//需要添加的数据
		foreach($this->params['data'] as $k => $v) {
			if(is_numeric($k)) {
				$createAgencyInfo[] = array(
					'o_uid'			=> $this->params['o_uid'],
					'o_depart_id'	=> $k,
					'a_uid'			=> $this->params['data']['str_' . $k]
				);
			}
		}
		
		//var_dump($deleteAgencyIds, $updateAgencyInfo, $createAgencyInfo);exit;
		
		//增，改，删用户代理数据
		$multiClient = $this->getMultiClient();
		if(!empty($deleteAgencyIds)) {
			$multiClient->call('workflowatom', 'user/user_agency_delete', array(
				'aid'	=> $deleteAgencyIds
			), 'deleteAgency');
		}
		if(!empty($updateAgencyInfo)) {
			$multiClient->call('workflowatom', 'user/user_agency_update', array(
				'data'	=> $updateAgencyInfo
			), 'updateAgency');
		}
		if(!empty($createAgencyInfo)) {
			$multiClient->call('workflowatom', 'user/user_agency_create', array(
				'data'	=> $createAgencyInfo
			), 'createAgency');
		}
		$multiClient->callData();
		
		$error_msg = '';
		
		if(!empty($multiClient->deleteAgency['content']['error_msg'])) {
			$error_msg .= 'deleteAgency：' . $multiClient->deleteAgency['content']['error_msg'] . '<br />';
		}
		if(!empty($multiClient->updateAgency['content']['error_msg'])) {
			$error_msg .= 'updateAgency：' . $multiClient->updateAgency['content']['error_msg'] . '<br />';
		}
		if(!empty($multiClient->createAgency['content']['error_msg'])) {
			$error_msg .= 'createAgency：' . $multiClient->createAgency['content']['error_msg'] . '<br />';
		}
		
		if(!empty($error_msg)) {
			return $this->app->response->setBody(Response::gen_error(10000, $error_msg));
		}
		
		//var_dump($multiClient->deleteAgency, $multiClient->updateAgency, $multiClient->createAgency);exit;
		
		$this->app->response->setBody(Response::gen_success('保存成功'));
	}
	
	private function _init() {
		$this->params['depart_id'] = isset($this->request->POST['depart_id']) ? $this->request->POST['depart_id'] : array();
		$this->params['data'] = array();
		$this->params['o_uid'] = isset($this->request->POST['o_uid']) ? $this->request->POST['o_uid'] : 0;
		
		if( !empty($this->params['depart_id']) ) {
			foreach($this->params['depart_id'] as $v) {
				$this->params['data'][$v] = '';
				if(!empty($this->request->POST['agency_' . $v])) {
					$agencyInfo = empty($this->request->POST['agency_' . $v]) ? array() : explode(',', $this->request->POST['agency_' . $v]);
					
					$this->params['data'][$v] = array_unique($agencyInfo);
					sort( $this->params['data'][$v], SORT_NUMERIC );
					$this->params['data']['str_' . $v] = implode(',', $this->params['data'][$v]);
				}
			}
		}
		
		if(empty($this->params['o_uid'])) {
			$this->app->response->setBody(Response::gen_error(10000, '参数异常'));
			return FALSE;
		}
		
		return TRUE;
	}
}