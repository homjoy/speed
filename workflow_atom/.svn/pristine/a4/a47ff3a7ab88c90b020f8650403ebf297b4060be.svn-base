<?php
namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\Tree as TreeObj;
use WorkFlowAtom\Package\Treeuserrole\UserRole ;
use WorkFlowAtom\Package\Tree\TreePermissions ;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class TreeByUid extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {
        $this->_init();

        //参数校验
        if ($this->query()->hasError()) {
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        if (empty($this->params['user_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $roleIds = $treeIds = $treeInfo =  array();

        //得到角色id
        $userRoleInfo = UserRole::getInstance()->getDataList($this->params);
        if (!empty($userRoleInfo)) {
            $roleIds = ArrayUtilities::my_array_column($userRoleInfo, 'role_id');
        }
        if (!empty($roleIds)) {
            //得到菜单
            $treePermissionsParams['role_id'] = $roleIds;
            if (!empty($this->params['status'])) {
                $treePermissionsParams['status'] = $this->params['status'];
            }
            $permissionsInfo = TreePermissions::getInstance()->getDataList($treePermissionsParams);
            if (!empty($permissionsInfo)) {
                $treeIds = ArrayUtilities::my_array_column($permissionsInfo, 'tree_id');
                $parentIds = ArrayUtilities::my_array_column($permissionsInfo, 'parent_id');

                $treeIds = array_merge($treeIds,$parentIds);
            }
        }

        if (!empty($treeIds)) {
            $treeParams['tree_id'] = $treeIds;
            if (!empty($this->params['status'])) {
                $treeParams['status'] = $this->params['status'];
            }
            $treeInfo = TreeObj::getInstance()->getDataList($treeParams);
        }

        if ($treeInfo === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($treeInfo)) {
            $return = Response::gen_error(50002);
        } else {
            $this->sample = TreeObj::getInstance()->getFields();           
            $return = Response::gen_success(Format::outputData($treeInfo, $this->sample, true));
        }

        $this->app->response->setBody($return); 
    }

    private function formatTree($data, $pid = 0) { 

        if (empty($data)) {
            return false;
        }

        $tree = array();
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $pid) {
                //父亲找到儿子
                $v['parent_info'] = $this->formatTree($data, $v['tree_id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    private function _init() {

        $this->rules = array(
            'user_id'   => array(
                'required'  => true,
                'type'      => 'integer',
                'default'   => 0,
            ),
            'status'   => array(
                'required'  => false,
                'type'      => 'integer',
            ),
        );
        
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}