<?php

namespace Admin\Modules\Auth\Tree;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Libs\Util\Format;
use Libs\Util\ArrayUtilities;

class BatchPermissionsAdd extends BaseModule {

    private $params = array();
    private $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {

        $this->_init();
        //参数校验
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }

        if (empty($this->params['role_id']) || empty($this->params['tree_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }
        if (!empty($this->params['role_id'])) {
            $permissions_params = array('role_id' => $this->params['role_id']);
        } else {
            $permissions_params = array();
        }
        if (empty($permissions_params)) {

            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', 'role_id 为空');
            return $this->app->response->setBody($return);
        }

        $multiClient = self::getMultiClient();
        $multiClient->call('workflowatom', 'tree/tree', array(), 'treeInfo');
        $multiClient->call('workflowatom', 'treerole/role_by_param', array('role_id' => $this->params['role_id'], 'status' => array(0, 1)), 'role');
        //查询已有的权限组

        $multiClient->call('workflowatom', 'tree/tree_permissions', $permissions_params, 'treePermissionsInfo');
        $multiClient->callData();
        //获取所有的树
        $treeInfo = $this->parseApiData($multiClient->treeInfo);

        $role = current($this->parseApiData($multiClient->role));
        //判断tree、role、permission有效性，若tree或role无效，permission应为无效
        if ($role['status'] == 0) {
            $res = array(
                'code' => 400,
                'error_msg' => 'tree有效性不一致',
            );

            return $this->app->response->setBody($res);
        }
        //获取已经存在的权限
        $treePermissionsInfo = $this->parseApiData($multiClient->treePermissionsInfo);
        if (!empty($treePermissionsInfo)) {
            //整理为tree_id 为key的数据 
            $treePermissInfo = ArrayUtilities::hashByKey($treePermissionsInfo, 'tree_id');
            //已经存在的权限 ,且状态不为 del的

            foreach ($treePermissInfo as $perValue) {
                if ($perValue['status'] != 0) {
                    $existsPermission[] = $perValue['tree_id'];
                }
            }
        } else {
            $treePermissInfo = array();
            $existsPermission = array();
        }


        //删除的元素
        $delPermission = array_diff($existsPermission, $this->params['tree_id']);

        $delReturn = $addReturn = array();
        if (!empty($delPermission)) {
            $delReturn = $this->_delPermission($delPermission, $treePermissInfo);
        }
        //新增的元素（包含新增和更新的）
        $addPermission = array_diff($this->params['tree_id'], $existsPermission);
        if (!empty($addPermission)) {
            $addReturn = $this->_addPermission($addPermission, $treeInfo, $treePermissInfo, $this->params['role_id']);
        }

        if (empty($delReturn) && empty($addReturn)) {
            $res = array(
                'code' => 200,
                'url' => '/auth/tree/tree_permissions',
            );
        } else if (empty($delReturn) && !empty($addReturn)) {
            $res = array(
                'code' => 400,
                'error_msg' => '增加权限' . implode(',', $addReturn) . '失败',
            );
        } else if (!empty($delReturn) && empty($addReturn)) {
            $res = array(
                'code' => 400,
                'error_msg' => '删除权限' . implode(',', $addReturn) . '失败',
            );
        }

        $this->app->response->setBody($res);
    }

    /**
     * 权限组删除
     * @param $delArray
     * @param $exitPermission
     *
     * @return array
     */
    protected function _delPermission($delArray, $exitPermission) {
        $del_params = array(
            'status' => 0,
        );

        $error_params = array();
        if (!empty($delArray)) {
            foreach ($delArray as $del_val) {
                if (!empty($exitPermission[$del_val])) {
                    if ($exitPermission[$del_val]['status'] != 0) {//未被删除，走删除操作
                        $del_params['id'] = $exitPermission[$del_val]['id'];
                        $del_params['tree_id'] = $del_val;
                        $del_params['role_id'] = $exitPermission[$del_val]['role_id'];
                        $del_params['parent_id'] = $exitPermission[$del_val]['parent_id'];
                        $result = self::getClient()->call('workflowatom', 'tree/tree_permissions_add', $del_params);

                        if ($result['httpcode'] != 200 || $result['content']['error_code'] != 0) {
                            $error_params[] = $del_val;
                        }
                    }
                }
            }
        }
        return $error_params;
    }

    /**
     * 权限组增加
     * @param $addArray
     * @param $treeInfo
     * @param $exitPermission 存在的权限
     * @param $role_id 权限id
     *
     * @return array
     */
    protected function _addPermission($addArray, $treeInfo, $exitPermission, $role_id) {
        $add_params = array(
            'status' => 1
        );
        $error_params = array();
        foreach ($addArray as $val) {
            //之前被删除的权限回复
            if (!empty($exitPermission[$val])) {
                $add_params['id'] = $exitPermission[$val]['id'];
                $add_params['tree_id'] = $val;
                $add_params['role_id'] = $role_id;
                $add_params['parent_id'] = $exitPermission[$val]['parent_id'];
                $result = self::getClient()->call('workflowatom', 'tree/tree_permissions_add', $add_params);

                if ($result['httpcode'] != 200 || $result['content']['error_code'] != 0) {
                    $error_params[] = $val;
                }
                continue;
            }
            //新增
            if (!empty($treeInfo[$val])) {
                $add_params['id'] = 0;
                $add_params['tree_id'] = $val;
                $add_params['role_id'] = $role_id;
                $add_params['parent_id'] = $treeInfo[$val]['parent_id'];
                $result = self::getClient()->call('workflowatom', 'tree/tree_permissions_add', $add_params);
                if ($result['httpcode'] != 200 || $result['content']['error_code'] != 0) {
                    $error_params[] = $val;
                }
            }
        }
        return $error_params;
    }

    protected function _init() {

        $this->rules = array(
            'role_id' => array(
                'required' => true,
                'type' => 'integer',
                'default' => 0,
            ),
            'tree_id' => array(
                'required' => true,
                'type' => 'multiId',
            ),
        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
