<?php
namespace Admin\Modules\Config;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Core\Config;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxSearchConfig
 * Date: 15/11/25
 * Time: 下午12:18
 */
class AjaxGetConfig extends BaseModule {
    protected $errors = NULL;
    private $id = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
        $this->_init();
        //参数处理
        if(empty($this->id)){
            $return =   Response::gen_error(10001,'没有信息可查询');
            return  $this->app->response->setBody($return);
        }
        $result=array();
        $result= Config::getInstance()->configList($this->id);
        $result =is_array($result)?array_pop($result):'';
        if($result === FALSE) {
            $return = Response::gen_error(50001,'搜索信息失败');
        }else{
            if(isset($result['father_id'])&&$result['father_id']!==0){

                $father= Config::getInstance()->configList(
                    array(
                        'id'=>$result['father_id']
                    )
                );
                $father =is_array($father)?array_pop($father):'';
                $result['father_path'] =isset($father['path'])?$father['path']:'';
            }else{
                $result['father_path'] ='/';
            }
            $result = array($result);
            $return = Response::gen_success($result);
        }
        $this->app->response->setBody($return);

    }
    public function _init(){
        $this->rules = array(
            'id'=>array(
                'required'=>true,
                'type'=>'integer',
            ),
        );
        $this->id = $this->query()->safe();
    }

}