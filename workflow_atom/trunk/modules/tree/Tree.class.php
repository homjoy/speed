<?php
namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\Tree as TreeObj;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class Tree extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {

        $parentId = array();
        $data = TreeObj::getInstance()->getDataList();

//var_dump($data);exit;
        $treeData = $this->formatTree($data);

        if (!empty($data)) {

            foreach ($data as $list) {
                if ($list['parent_id'] > 0) {
                    $parentId[] = $list['parent_id'];
                }
            }

            if (!empty($parentId)) {
                $parentTree = TreeObj::getInstance()->getDataList(array('tree_id' => $parentId));
            }

            if (!empty($parentTree)) { 
                foreach ($data as &$val) {
                    if ($val['parent_id'] > 0) {
                        $val['parent_info'] = $parentTree[$val['parent_id']];
                    }
                }
            }
        }

        $this->app->response->setBody(array('data' => $data, 'treeData' => $treeData));    
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
        return true;
    }
}