<?php
namespace Admin\Modules\Auth\Role;

use Libs\Util\ArrayUtilities;
use Admin\Package\Common\Response;

class UserRole extends \Admin\Modules\Common\BaseModule {

    protected $checkUserPermission = TRUE;
    
    public function run() {

        $data = array();
        //获取当前用户的权限
        $result = self::getClient()->call('workflowatom', 'treeuserrole/user_role_by_uid', array('user_id' => $this->user['id'], 'status' => 1));

        if ($result['httpcode'] == 200 && !empty($result['content'])) {
            $data = $result['content']['data'];
            
            //超级管理员
            $roleId = ArrayUtilities::my_array_column($data, 'role_id');
           
            if (!in_array('1', $roleId)) {
                $return = Response::gen_error(50001);
                return $this->app->response->setBody(array('data' => $data));
            }
        } else if ($result['httpcode'] == 200 && $result['content']['code'] ==400) {
            $data = $result['content'];
            return $this->app->response->setBody(array('data' => $data));
        }
        
        //tree_user_role
        $userRoleInfo = self::getClient()->call('workflowatom', 'treeuserrole/user_role', array('order' => array('role_id' => 'asc')));
        if ($userRoleInfo['httpcode'] == 200 &&  $userRoleInfo['content']['code'] == 200) {
            $userRoleInfo = $userRoleInfo['content']['data'];
        }

        $userId = ArrayUtilities::my_array_column($userRoleInfo, 'user_id');
        $userId = array_unique($userId);
       
        $multiClient = self::getMultiClient();

        $multiClient->call('workflowatom', 'treerole/role', array(), 'roleInfo');
        $multiClient->call('atom', 'account/get_user_info', array('user_id' => implode(',', $userId), 'all' => 1, 'status' => array(1, 2, 3)), 'userInfo');
        $multiClient->callData();

        $roleInfo = $this->parseApiData($multiClient->roleInfo);
        $userInfo = $this->parseApiData($multiClient->userInfo);
        
        $result['data'] = $userRoleInfo;
        $result['roleInfo'] = !empty($roleInfo) ? $roleInfo : array();
        $result['userInfoAll'] = !empty($userInfo) ? $userInfo : array();
        
        $this->app->response->setBody($result);
    }
}