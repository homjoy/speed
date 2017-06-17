<?php
namespace Admin\Modules\Config;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Core\Config;
/**这个为了支持拿到节点 和为了拿到父亲节点
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxSearchConfig
 * Date: 15/11/25
 * Time: 下午12:18
 */
class AjaxSearchConfig extends BaseModule {
    protected $errors = NULL;
    private $params = NULL;
    private $all = 1;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {
        $this->_init();
        //参数处理
        if(empty($this->params['path'])&&$this->params['father_id']===FALSE){
            $return =   Response::gen_error(10001,'没有信息可查询');
            return  $this->app->response->setBody($return);
        }
        $this->params['all'] =$this->all;

        $result = Config::getInstance()->ConfigSearch($this->params);
        if($result == FALSE) {
            $return = Response::gen_error(50001,'搜索信息失败');
        }else{
            //返回值处理 一种path  一种father_id
            $return=array();
            if(!isset($this->params['father_id'])){
                $return[]=array( //对一种path顶级节点处理
                    'name'=>'/',
                    'id'=>0
                );
            }
            foreach($result as $k =>$v){
                $ret['name'] =isset($v['path'])?$v['path']:'';
                $ret['id']=isset($v['id'])?$v['id']:'';
                $return[]=$ret;
            }
            $return = Response::gen_success($return);

        }
        $this->app->response->setBody($return);

    }

    private function _init() {
        $data = $this->request->GET;
        $data_check = array(
                'path'			=> array(
                    'type'		=> 'string',
                    'maxLength'	=> 100,
                ),
                'father_id'  => array(
                    'type'    => 'integer',
                ),

            );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->query()->safe();
    }

}