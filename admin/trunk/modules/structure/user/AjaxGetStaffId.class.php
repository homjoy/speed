<?php
namespace Admin\Modules\Structure\User;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;

/**
 * 获取staffId
 * Class AjaxGetStaffId
 * @package Admin\Modules\Structure\User
 * User: GUOJEIZHU
 * Date: 2016/04/11
 */
class AjaxGetStaffId extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    
    public function run() {

        $this->_init();
        if($this->params['staff_id_prefix'] >=65  && $this->params['staff_id_prefix'] <=90)
        {
            $staff_id_prefix = chr($this->params['staff_id_prefix']);
            $params = array(
                'staff_id_prefix' => $staff_id_prefix
            );
            $staff_info = UserInfo::getInstance()->getMaxStaffId($params);
            if(!empty($staff_info)){
                $new_staff_info = current($staff_info);
                $max_staff_id = $new_staff_info['staff_id'];
                $max_staff_inter = substr($max_staff_id,1);
                $new_max_staff_inter =intval($max_staff_inter)+1;
                if(strlen($new_max_staff_inter) <6){
                    $new_max_staff_inter = str_pad($new_max_staff_inter,6,'0',STR_PAD_LEFT);
                }
                $new_max_staff_inter = $staff_id_prefix.$new_max_staff_inter;
                return $this->app->response->setBody(Response::gen_success($new_max_staff_inter));
            }

        }
        return $this->app->response->setBody(Response::gen_error(50001,'修改失败'));

    }

    private function _init() {
        $this->rules = array(
            'staff_id_prefix' => array( //前缀 允许A-Z
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type' => 'string',
            ),

        );

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }
    
}