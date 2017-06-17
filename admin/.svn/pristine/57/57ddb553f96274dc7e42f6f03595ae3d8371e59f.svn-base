<?php
namespace Admin\Modules\Im\App;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Im\ImAppVersion;
/**
 * 添加
 *
 * @author anqiliu@meilishuo.com
 * @since  2015-11-27
 */
class AjaxAppEdit extends BaseModule
{

    protected $errors = null;
    private $params = null;

    public static $VIEW_SWITCH_JSON = TRUE;
    public function run()
    {
        $this->_init();
        
        $return = ImAppVersion::getInstance()->update($this->params);
        

        if(empty($return) ){
            $return = Response::gen_error(Response::DS_DATA_NO_DATA,'','更新成员失败');
            return $this->app->response->setBody($return);
        }else{
            $return = Response::gen_success('修改成功');
            return $this->app->response->setBody($return);
        }
    }

   
    
    private function _init()
    {

        $this->rules = array(
                'id' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_type' => array(
                    'required' => FALSE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_code' => array(
                    'required' => FALSE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_name' => array(
                    'required' => FALSE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_md5' => array(
                    'required' => FALSE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_url' => array(
                    'required' => FALSE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                
               
        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}
