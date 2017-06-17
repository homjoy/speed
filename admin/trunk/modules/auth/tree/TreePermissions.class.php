<?php
namespace Admin\Modules\Auth\Tree;

use Libs\Util\ArrayUtilities;

class TreePermissions extends \Admin\Modules\Common\BaseModule {
    
    protected $checkUserPermission = TRUE;

	public function run() {
        
        $role = $tree = $treePermissions = array();

        $multiClient = self::getMultiClient();

        $multiClient->call('workflowatom', 'tree/tree', array(), 'treeInfo'); $result = self::getClient()->call('workflowatom', 'tree/tree', array());
        $multiClient->call('workflowatom', 'treerole/role', array(), 'roleInfo');
        $multiClient->call('workflowatom', 'tree/tree_permissions', array(), 'treePermissionsInfo');
        $multiClient->callData();

        $treeInfo = $multiClient->treeInfo;
        $roleInfo = $multiClient->roleInfo;
        $treePermissionsInfo = $multiClient->treePermissionsInfo;
      
        if ($treeInfo['httpcode'] == 200 && !empty($treeInfo['content'])) {
            $tree = $treeInfo['content']['data'];
            $treeData = $treeInfo['content']['treeData'];
        }

        if ($roleInfo['httpcode'] == 200 && !empty($roleInfo['content'])) {
            $role = $roleInfo['content']['data'];
        }

        if ($treePermissionsInfo['httpcode'] == 200 && !empty($treePermissionsInfo['content'])) {
            $treePermissions = $treePermissionsInfo['content']['data'];
        }

        $treeData = $this->formateTreeName($treeData);

		$this->app->response->setBody(array('role' => $role, 'tree' => $tree, 'treeData' => $treeData, 'treePermissions' => $treePermissions));	
    }

    private function formateTreeName($tree, $pre = '') {

        $res = array();
        if (!empty($tree)) {
            foreach ($tree as $k => $list) {
                $parentInfo = $list['parent_info'];
                unset($list['parent_info']);

                if ($list['parent_id'] > 0) {
                    $list['tree_name'] = $pre . $list['tree_name'];
                }
                
                $res[$list['tree_id']] = $list;

                if (!empty($parentInfo)) {
                    $tmp = $this->formateTreeName($parentInfo, $pre . '--');
                    foreach ($tmp as $key => $val) {
                        $res[$val['tree_id']] = $val;
                    }
                } 
            }
        }

        return $res;
    }
}