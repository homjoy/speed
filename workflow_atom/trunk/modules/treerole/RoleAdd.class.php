<?php
namespace WorkFlowAtom\Modules\Treerole;

use WorkFlowAtom\Package\Treerole\Role ;
use WorkFlowAtom\Package\Common\Response;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;

class RoleAdd extends \WorkFlowAtom\Modules\Common\BaseModule {

    protected $params = array();

    public function run() {
        $this->_init();

        $this->sample = Role::getInstance()->getFields();

        //参数校验
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $treeInfo = Role::getInstance()->getDataList(array(
            'role_name'     => $this->params['role_name'],
        ));

        if (empty($this->params['role_id'])) {
            unset($this->params['role_id']);
            
            if (!empty($treeInfo)) {
                $return = Response::gen_error(10002, '', '该角色已存在！');
                return $this->app->response->setBody($return);
            }
            
            $result = Role::getInstance()->insert($this->params);
        } else { 

            if (!empty($treeInfo)) {
                $return = Response::gen_error(10002, '', '该角色已存在！');
                return $this->app->response->setBody($return);
            }
            
            $result = role::getInstance()->update($this->params);
        }

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($result)) {
            $return = Response::gen_error(50012);
        } else {
            if (!isset($this->params['role_id'])) {
                $this->params['role_id'] = $result;
            }            
            $return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

        $this->app->response->setBody($return);  
    }

    private function _init() {
        $this->rules = array(
            'role_id'   => array(
                'required'  => false,
                'type'      => 'integer',
                'default'   => 0,
            ),
            'role_name' => array(
                'required'      => true,
                'type'          => 'string',
                'maxLength'     => 30,
                'allowEmpty'    => false
            ),
            'status'    => array(
                'type'      => 'integer',
                'default'   => 1,
            ),
        );
        
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }
}