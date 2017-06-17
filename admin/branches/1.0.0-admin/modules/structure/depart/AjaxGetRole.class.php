<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\RoleInfo;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  role
 * Date: 2015-10-08
 * Time: 下午12:18 title
 */
class AjaxGetRole extends BaseModule {

    protected $errors = NULL;
    protected $all = 1;
    protected $status = 1;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {
        $role = $temp =array();
        $role = RoleInfo::getInstance()->getRoleInfo(array('all'=>$this->all,'status'=>$this->status));
        foreach($role as $key =>$value){
            if($value['role_id']){
                $temp[$key]['role_id'] = $value['role_id'];
            }
            if($value['role_name']){
                $temp[$key]['role_name'] = $value['role_name'];
            }
        }
        if($temp===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'获取角色失败'));
        }
        return $this->app->response->setBody(Response::gen_success($temp));
    }

}
