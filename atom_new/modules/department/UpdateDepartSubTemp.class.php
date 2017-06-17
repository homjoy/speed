<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentSubTemp;

/**
 * 部门替换更新
 * @author hongzhou edit from haibinzhou@meilishuo.com
 * @date 2015-10-09
 */
class UpdateDepartSubTemp extends BaseModule {
    
    private $params = array();

    public function run() {

        $this->_init();
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result = DepartmentSubTemp::model()->updateById($this->params);

        if($result === FALSE) {
            $return = Response::gen_error(10004);
        }else if(empty($result)){
            $return  = array();
        }else {

            $return = Response::gen_success($result);
        }

        $this->app->response->setBody($return);
    }
    
    
    private function _init() {
        
        $post = $this->request->POST;
        
        $this->rules = array(
            'sub_id' => array(
                'type'=>'integer',
            ),
            'relation_id'=>array(
                'type'=>'integer',
            ),
            'user_id' => array(
                'type'=>'integer',
            ),
            'memo' => array(
                'type' => 'string',
            ),
            'status' => array(
                'type'=>'integer',
                'default' => 1,
            ),
        );
        
        $safe_post = $this->post()->safe();
        $this->params = array_intersect_key($safe_post, $post);
        if(empty($this->params['relation_id']) && empty($this->params['depart_id'])){
            return FALSE;
        }

        return TRUE;
    }
    
}