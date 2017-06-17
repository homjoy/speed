<?php
namespace WorkFlowAtom\Modules\Treerole;

use WorkFlowAtom\Package\Treerole\Role as RoleObj;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class RoleByParam extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {
        $this->_init();

        $this->sample = RoleObj::getInstance()->getFields();
        
        //参数校验
        if (empty($this->params['role_id']) && empty($this->params['role_name'])) {

            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $params = array('status' => $this->params['status']);
        unset($this->params['status']);
        foreach ($this->params as $key => $list) {
            if (!empty($list)) {
                
                $tmp = is_array($list) ? $list : explode(',', $list);

                foreach ($tmp as $val) {
                    switch ($key) {
                        case 'role_id':
                            if ((int)$val > 0) {
                                $params['role_id'][] = $val;
                            }
                            break;
                        case 'role_name':
                            if (trim($val)) {
                                $params['role_name'][] = trim($val);
                            }
                    }
                }
            }
        }

        $data = RoleObj::getInstance()->getDataList($params);

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
            'role_id'   => array(
                'required'  => false,
                'type'      => 'string',
                'default'   => 0,
            ),
            'role_name' => array(
                'required'      => false,
                'type'          => 'string',
                'maxLength'     => 30,
                'allowEmpty'    => false
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