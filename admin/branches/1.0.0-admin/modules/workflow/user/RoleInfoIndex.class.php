<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 展示所有角色信息
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-10
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class RoleInfoIndex extends BaseModule {

    protected $checkUserPermission = TRUE;

	public function run() {

		$result = self::getClient()->call('workflowatom', 'user/role_info_list', array('status' => array(0, 1)));
		$result = $this->parseApiData($result);

		if ($result !== FALSE) {
			$this->app->response->setBody(Response::gen_success($result));
		}
	}
}