<?php
namespace Admin\Modules\Auth\Tree;

use Libs\Util\ArrayUtilities;

class Tree extends \Admin\Modules\Common\BaseModule {

    protected $checkUserPermission = TRUE;
    
	public function run() {
        
        $data = $tree = array();

        $result = self::getClient()->call('workflowatom', 'tree/tree', array());

        if ($result['httpcode'] == 200 && !empty($result['content'])) {
            $data = $result['content']['data'];
            $treeData = $result['content']['treeData'];
            $tree = $this->formateTreeName($treeData);
        }

		$this->app->response->setBody(array('data' => $data, 'tree' => $tree));
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