<?php
namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\Tree as TreeObj;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class TreeByParam extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {
        $this->_init();

        $this->sample = TreeObj::getInstance()->getFields();
        //参数校验
        if (empty($this->params['tree_id']) && empty($this->params['tree_name']) 
            && empty($this->params['tree_url']) && empty($this->params['status']) && empty($this->params['display_position'])) {

            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $params = array('status' => $this->params['status']);
        $params['display_position'] = $this->params['display_position'];
        unset($this->params['status']);
        unset($this->params['display_position']);
        foreach ($this->params as $key => $list) {
            if (!empty($list)) {
                
                $tmp = is_array($list) ? $list : explode(',', $list);
                
                foreach ($tmp as $val) {
                    switch ($key) {
                        case 'tree_id':
                            if ((int)$val > 0) {
                                $params['tree_id'][] = $val;
                            }
                            break;
                        case 'tree_name':
                            if (trim($val)) {
                                $params['tree_name'][] = trim($val);
                            }
                            break;
                        case 'tree_url':
                            if (trim($val)) {
                                $params['tree_url'][] = trim($val);
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
            'tree_id'   => array(
                'required'  => false,
                'type'      => 'string',
                'default'   => 0,
            ),
            'tree_name' => array(
                'required'      => false,
                'type'          => 'string',
                'maxLength'     => 255,
                'allowEmpty'    => false
            ),
            'tree_url' => array(
                'required'      => false,
                'type'          => 'string',
                'maxLength'     => 255,
                'allowEmpty'    => false
            ),
            'status'    => array(
                'type'      => 'integer',
                'default'   => 1,
            ),
            'display_position' => array(
                'type'    => 'integer' , 
                'default' => 1,
            ),
        );
        
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}