<?php

namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\TreePermissions as TreeObj;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class TreePermissions extends \WorkFlowAtom\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run() {
        $this->_init();
        // if (!$this->_init()) {
        //     return FALSE;
        // }
        $this->sample = TreeObj::getInstance()->getFields();
        $parentId = array();
        $params = array();
        if (!empty($this->params)) {
            foreach ($this->params as $key => $list) {
                $tmp = explode(',', $list);
                foreach ($tmp as $val) {
                    switch ($key) {
                        case 'tree_id':
                            if ((int) $val > 0) {
                                $params['tree_id'][] = $val;
                            }
                            break;
                        case 'role_id':
                            if (trim($val)) {
                                $params['role_id'][] = trim($val);
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        $data = TreeObj::getInstance()->getDataList($params);

        if ($data === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($data)) {
            $return = Response::gen_error(30001);
        } else {
            $return = Response::gen_success(Format::outputData($data, $this->sample, true));
        }

        $this->app->response->setBody($return);
    }

    private function _init() {
        $this->rules = array(
            //以逗号分隔

            'role_id' => array(
                'type' => 'string',
                'default' => 0,
            ),
        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
