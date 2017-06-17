<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\TitleInfo;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxAddTitle
 * Date: 15/9/25
 * Time: 下午12:18
 */
class AjaxAddTitle extends BaseModule {
    protected $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {

        $this->_init();
        if( $this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }
        $result = $title_info  = TitleInfo::getInstance()->createTitleInfo($this->params);
        if(empty($result)){
         return $this->app->response->setBody(Response::gen_error(50001,'创建职位失败'));
        }
        $this->app->response->setBody(Response::gen_success('创建成功'));
    }

    private function _init() {

        $this->rules = array(

            'depart_id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
                'maxLength'=> 30,
            ),
             'title_name' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'title_info' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'memo' => array(
                'type'=>'string',
                'maxLength'=> 30,
            ),
            'status' => array(
                'type'=>'integer',
                'default'=> 1,
            ),


        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();

    }
}