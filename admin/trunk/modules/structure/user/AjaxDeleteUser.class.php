<?php
namespace Admin\Modules\Structure\User;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * DeleteUser
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class AjaxDeleteUser extends BaseModule {
    private $params = array();
    protected $errors = array();
    public static $VIEW_SWITCH_JSON = TRUE;
    const USER_TYPE = 4;

    public function run() {

        $this->_init();
        if(empty($this->params['user_id'])){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
        $ret =  UserInfo::getInstance()->updateUserInfo($this->params);
        if($ret ===FALSE  || empty($ret)){
          return $this->app->response->setBody(Response::gen_error(50004, '删除失败'));
        }
        $this->doLog($this->params);
        $this->app->response->setBody(Response::gen_success('删除成功'));
    }
    protected function doLog($new_param=array(),$old_param='delete'){

        $ret = Log::getInstance()->createLogs(array(
                'user_id'=>$this->user['id'],
                'handle_id'=>isset($new_param['user_id'])?$new_param['user_id']:'',
                'operation_type'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>self::USER_TYPE
            )
        );
        return $ret;
    }
    private function _init() {
        $this->rules = array(//user_info
            'user_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
             'status' => array(
                'type'=>'integer',
                'default'=>2,
            ),

        );
        $this->params   = $this->post()->safe();
        $this->errors   = $this->post()->getErrors();
    }
}