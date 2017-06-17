<?php
namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\TreePermissions;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class TreePermissionsByParam extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {
        $this->_init();

        $this->sample = TreePermissions::getInstance()->getFields();
        
        //参数校验
        if (empty($this->params['tree_id']) && empty($this->params['role_id'])) {

            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $params = array('status' => $this->params['status']);
        foreach ($this->params as $key => $list) {
            $tmp = explode(',', $list);
            foreach ($tmp as $val) {
                switch ($key) {
                    case 'tree_id':
                        if ((int)$val > 0) {
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
        
        $data = TreePermissions::getInstance()->getDataList($params);

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
            'tree_id'   => array(
                'required'  => false,
                'type'      => 'string',
                'default'   => 0,
            ),
            'role_id'   => array(
                'required'  => false,
                'type'      => 'string',
                'default'   => 0,
            ),
            'status'    => array(
                'type'      => 'integer',
                'default'   => 1,
            )
        );
        
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}