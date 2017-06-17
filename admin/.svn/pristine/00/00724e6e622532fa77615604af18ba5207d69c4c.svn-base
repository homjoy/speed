<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserInfo;
use Admin\Package\Department\Department;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS 请求备份并把department_info数据下载到department_info_temp表
 * Date: 2015-10-08
 * Time: 下午12:18 title
 */
class AjaxRequestDb extends BaseModule {

    protected $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {

        $this->_init();
        $title=$push_temp=array();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        //记录操作者id
        $this->user['id'] .=$this->params['title'].':';
        $this->params['title'] = $this->user['id'];
        $title = self::getClient()->call('atom', 'core/create_data_backup',$this->params);
        $title = $this->parseApiData($title);
        if(empty($title)){
           return   $this->app->response->setBody(Response::gen_error(50001,'没有备份成功'));
        }
        $push_temp =Department::getInstance()->backupAllDepartInfo();

        if(empty($push_temp)){
            return   $this->app->response->setBody(Response::gen_error(50001,'没有下载到临时表'));
        }
       $this->app->response->setBody(Response::gen_success('申请成功'));
    }
    public function _init() {
        $this->rules  = array(
        'title' => array(
            'required' => TRUE,
            'allowEmpty' => FALSE,
            'type'=>'string',
            'maxLength'=> 300,
        ) );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();

    }
}