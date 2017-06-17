<?php
namespace Admin\Modules\Auth\Tree;

/**
 * 权限添加
 */
use Libs\Util\ArrayUtilities;
use Admin\Package\Common\Response;

class PermissionsAdd extends \Admin\Modules\Common\BaseModule {
    
    protected $checkUserPermission = TRUE;
    protected $params = array();
    protected $error = '';
	public function run() {

        $this->init();
        $role = $tree = $treePermissions = array();
        if(!empty($this->params['role_id'])) {
            $permissions_params = array('role_id' => $this->params['role_id']);
        }else{
            $permissions_params = array();
        }
        if(empty($permissions_params)){
            exit('role_id 为空');
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', 'role_id 为空');
            return $this->app->response->setBody($return);
            
        }
        $multiClient = self::getMultiClient();

        $multiClient->call('workflowatom', 'tree/tree', array('status' =>1), 'treeInfo');
        $multiClient->call('workflowatom', 'treerole/role', array(), 'roleInfo');

        //查询已有的权限组
        $multiClient->call('workflowatom', 'tree/tree_permissions_by_param', $permissions_params, 'treePermissionsInfo');
        $multiClient->callData();
        //所有的树
        $treeInfo = $multiClient->treeInfo;
        //获取所有的role
        $roleInfo = $multiClient->roleInfo;
        if ($roleInfo['httpcode'] == 200 && !empty($roleInfo['content'])) {
            $role = $roleInfo['content']['data'];
            if(empty($role[$this->params['role_id']])){
                exit('role_id 为空');
            }
            $role_name = $role[$this->params['role_id']];
         
        }
        
        $treePermissionsInfo = $multiClient->treePermissionsInfo;
        if ($treeInfo['httpcode'] == 200 && !empty($treeInfo['content'])) {
            $tree = $treeInfo['content']['data'];
            foreach($tree as $key => $value){
                if( 0 == $value['status'] ) {
                    unset($tree[$key]);
                }
            }
            $tree = $this->getTree(0,$tree);
            $treeData = $treeInfo['content']['treeData'];
        }

       
        $treePermissions = array(); 
        $has_tree_permissions  = array();
        if ($treePermissionsInfo['httpcode'] == 200 && !empty($treePermissionsInfo['content']) && !empty($treePermissionsInfo['content']['data'])) {
            $treePermissions = $treePermissionsInfo['content']['data'];
            $has_tree_permissions = ArrayUtilities::my_array_column($treePermissions,'tree_id');
        }

        //$treeData = $this->formateTreeName($treeData);

		$this->app->response->setBody(array( 'tree' => $tree['child'],'has_permissions' => $has_tree_permissions,'role_info' =>$role_name));	
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

     /**
     * 递归,获取菜单的上下级结构
     *
     * @param $tree_id
     * @param $data
     *
     * @return array
     */
    private function getTree($tree_id, $data)
    {//depart_id


        $params = $child = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($value['tree_id'] == $tree_id) {
                    $params = $value;
                }
                if ($value['parent_id'] == $tree_id) {
                    $child[$key] = $value;
                }
            }
        }


        $params['child'] = array();
        if (is_array($child)) {//child
            foreach ($child as $key => $value) {
                if (isset($value['tree_id'])) {
                    $params['child'][$value['tree_id']] = $this->getTree($value['tree_id'], $data);
                }

            }

        } else {
            $params['child'] = '';
        }

        return $params;

    }

    private function init(){
        $this->rules = array(
            'role_id'   => array(
                 'required' => true,
                'allowEmpty' => false,
                'type'      => 'integer',
                'default'   => 0,
            ),

        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}