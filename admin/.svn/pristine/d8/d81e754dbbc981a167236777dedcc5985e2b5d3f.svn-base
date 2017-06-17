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
class AjaxAppAdd extends BaseModule
{

    protected $errors = null;
    private $params = null;

    public static $VIEW_SWITCH_JSON = TRUE;
    public function run()
    {
        $this->_init();
        if(empty($this->params['force_update'])){
            $this->params['force_update'] = 0;
        } 
        if(empty($this->params['notes'])){
            $this->params['notes'] = '';
        } 
        if(!empty($this->params['v_id'])){
            $this->params['id'] = $this->params['v_id'];
        } 
        unset($this->params['v_id']);

        $return = ImAppVersion::getInstance()->insert($this->params);
      

        if(empty($return) ){
            $return = Response::gen_error(Response::DS_DATA_NO_DATA,'','创建成员失败');
            return $this->app->response->setBody($return);
        }else{
            $return = Response::gen_success('创建成功');
            return $this->app->response->setBody($return);
        }
    }

   
    
    private function _init()
    {

        $this->rules = array(
                'v_id' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'integer',
                ),
                'v_type' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_code' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_name' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_md5' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'v_url' => array(
                    'required' => TRUE,
                    'allowEmpty' => FALSE,
                    'type'=>'string',
                ),
                'force_update' => array(
                    'required' => TRUE,
                    'allowEmpty' => true,
                    'type'=>'integer',
                ),
                'notes' => array(
                    'required' => TRUE,
                    'allowEmpty' => true,
                    'type'=>'string',
                ),
                
               
        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}
