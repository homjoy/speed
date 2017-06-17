<?php 
namespace WorkFlowAtom\Modules\User;

use WorkFlowAtom\Modules\Common\BaseModule;
use WorkFlowAtom\Package\Common\Response;
use WorkFlowAtom\Package\User\UserRoleMap;
use Libs\Util\Format;

/**
 * 逻辑删除用户角色映射
 * @author jingjingzhang@meilishuo.com
 * @date 2015-7-16 
 */

class UserRoleMapDelete extends BaseModule {

    protected $map_id;
	
    public function run() {

    	$this->_init();

    	if ($this->post()->hasError()) {
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        if (empty($this->map_id['map_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = UserRoleMap::getInstance()->logicDelete($this->map_id['map_id']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        } else if (empty($result)) {
            $return = Response::gen_error(50004);
        } else {
   	        $return = Response::gen_success(array('msg' => '修改成功'));
        }

        $this->app->response->setBody($return);
    }

    private function _init() {

        $this->rules = array(
            'map_id' => array(
                'required'   => TRUE,
                'type'       => 'integer',
                'maxLength'  => 11,
            ),
        );
	    $this->map_id = $this->post()->safe();
    }
}