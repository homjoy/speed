<?php
namespace WorkFlowAtom\Modules\Tree;

use WorkFlowAtom\Package\Tree\Tree ;
use WorkFlowAtom\Package\Common\Response;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;

class TreeAdd extends \WorkFlowAtom\Modules\Common\BaseModule {

    protected $params = array();

    public function run() {
        $this->_init();

        $this->sample = Tree::getInstance()->getFields();

        //参数校验
        if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $treeInfo = Tree::getInstance()->getDataList(array(
            'tree_name' => $this->params['tree_name'],
            'tree_url'  => $this->params['tree_url']
        ));

        if (empty($this->params['tree_id'])) {
            unset($this->params['tree_id']);
            
            if (!empty($treeInfo)) {
                $return = Response::gen_error(10002, '', '该菜单已存在！');
                return $this->app->response->setBody($return);
            }
            
            $result = Tree::getInstance()->insert($this->params);
        } else {
            unset($treeInfo[$this->params['tree_id']]);
            
            if (!empty($treeInfo)) {
                $return = Response::gen_error(10002, '', '该菜单已存在！');
                return $this->app->response->setBody($return);
            }
            
            $result = Tree::getInstance()->update($this->params);
        }

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        } elseif (empty($result)) {
            $return = Response::gen_error(50012);
        } else {
            if (!isset($this->params['tree_id'])) {
                $this->params['tree_id'] = $result;
            }            
            $return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

        $this->app->response->setBody($return);  
    }

    private function _init() {

        $this->rules = array(
            'tree_id'   => array(
                'required'  => false,
                'type'      => 'integer',
                'default'   => 0,
            ),
            'tree_name' => array(
                'required'      => true,
                'type'          => 'string',
                'maxLength'     => 255,
                'allowEmpty'    => false
            ),
            'tree_url' => array(
                'required'      => true,
                'type'          => 'string',
                'maxLength'     => 255,
                'allowEmpty'    => false
            ),
            'status'    => array(
                'type'      => 'integer',
                'default'   => 1,
            ),
            'parent_id' => array(
                'type'      => 'integer',
                'default'   => 0,
            ),
            'display_position' => array(
                'type'      => 'integer',
                'default'   => 0,
            ),
        );
        
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }
}