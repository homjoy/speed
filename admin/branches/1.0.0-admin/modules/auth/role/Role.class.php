<?php
namespace Admin\Modules\Auth\Role;

use Libs\Util\ArrayUtilities;

class Role extends \Admin\Modules\Common\BaseModule {

	protected $checkUserPermission = TRUE;
	
	public function run() {
        
        $data = array();
        //获取当前用户的权限

        $result = self::getClient()->call('workflowatom', 'treerole/role', array());

        if($result['httpcode'] == 200 && !empty($result['content'])){
            $data = $result['content']['data'];
        }

		$this->app->response->setBody(array('data' => $data));		
    }
}