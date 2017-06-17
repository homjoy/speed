<?php
namespace Admin\Modules\Config;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Core\Config;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  ConfigHome
 * Date: 15/12/25
 * Time: 下午12:18
 */
class ConfigHome extends BaseModule {
     protected $errors = NULL;
     private $params = NULL;
     protected $checkUserPermisssion = TRUE;
     public function run() {

         $this->_init();

         $queryParams=array();
         if(!empty($this->params[ 'all' ])){
             $queryParams[ 'all' ] =$this->params[ 'all' ];
         }
         if($this->params[ 'father_id' ]!==FALSE){
             $queryParams[ 'father_id' ] =$this->params[ 'father_id' ];
         }
         $result = Config::getInstance()->configSearch($queryParams);

         if($result === FALSE) {
             $return = array();
         }else{
             $return=array();
             foreach($result as $k =>$v){
                 $ret['name'] =isset($v['path'])?$v['path']:'';
                 $ret['id']=isset($v['id'])?$v['id']:'';
                 $return[]=$ret;
             }
             $return = Response::gen_success($return);

         }
         $this->app->response->setBody($return);
    }

    private function _init() {//path  father_id

        $this->rules = array(
            'father_id'  => array(
                'type'    => 'integer',
                'default' => 0,
            ),
            'all'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}