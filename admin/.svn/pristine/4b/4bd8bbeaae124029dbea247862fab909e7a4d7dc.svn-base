<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
use Admin\Package\Account\UserInfo;
/**AjaxSearchDepart
 * @author hongzhou@meilishuo.com
 * @since 2015-9-25 下午12:53:13
 */
class AjaxSearchDepart extends BaseModule {

    protected $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {

        $this->_init();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result =$this->getDepartInfo($this->params);

        if($result===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'获取部门信息失败'));
        }
        //处理数据
        $ret = $ret_temp=array();
        foreach($result as $k=>$v){
            $ret_temp[$k]['depart_id'] =  isset($v['depart_id'])?$v['depart_id']:'';
            $ret_temp[$k]['name'] =  isset($v['depart_name'])?$v['depart_name']:'';
            $ret[] =$ret_temp[$k];
        }
        return $this->app->response->setBody(Response::gen_success($ret));

    }


    private function getDepartInfo($param) {
        if($param['type']==1){//从表
            $ret = Department::getInstance()->getDepartTemp($param);
        }else{//主表
            $ret = Department::getInstance()->getDepart( $param);
        }


        return $ret;
    }

    private function _init() {

        $this->rules = array(
            'depart_name' => array(//中文
                'required'   => TRUE,
                'allowEmpty' => FALSE,
                'type' => 'string',
            ),
            'match' => array(
                'type' => 'string',
                'default'=>'like'
            ),
            'status'  => array(
                'type'    => 'multiID',
                'default' => 1,
            ),
            'all'  => array(
                'type'    => 'integer',
                'default' =>1,
            ),
            'type'  => array(
                'type'    => 'integer',
                'enum' =>array(1,2),
                'default' =>2,//搜索主表
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }
}
