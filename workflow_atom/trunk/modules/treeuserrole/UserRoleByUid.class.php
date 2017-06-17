<?php
namespace WorkFlowAtom\Modules\Treeuserrole;

use WorkFlowAtom\Package\Treeuserrole\UserRole as RoleObj;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use WorkFlowAtom\Package\Common\Response;

class UserRoleByUid extends \WorkFlowAtom\Modules\Common\BaseModule {

     public function run() {

        $this->_init();
        $this->sample = RoleObj::getInstance()->getFields();

        //参数校验
        if ($this->query()->hasError()) {
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }

        if (empty($this->params['user_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $data = RoleObj::getInstance()->getDataList($this->params);

        if ($data === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($data)) {
            $return = Response::gen_error(30001);
        } else {
            $return = Response::gen_success(Format::outputData($data, $this->sample, TRUE));
        }

        $this->app->response->setBody($return); 
    }


    private function _init() {
        
        $this->rules = array(
            'user_id' => array(
                'required' => true,
                'type'     => 'integer',
                'default'  => 0,
            ),
            'status'  => array(
                'type' => 'integer',
            ),
        );
        
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}