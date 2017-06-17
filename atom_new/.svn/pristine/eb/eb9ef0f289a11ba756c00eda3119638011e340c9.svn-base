<?php
namespace Atom\Modules\Department;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentSub;

/**
 * 获取部门关系信息 或 批量获取
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-15
 */
class DepartSubList extends \Atom\Modules\Common\BaseModule{
    
    protected $params = array();
    protected $errors = array();


    public function run() {

        $this->_init();

        if(empty($this->params)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $ret = DepartmentSub::model()->getDataList($this->params, $this->params['offset'], $this->params['limit']);
        if ($ret === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success($ret);//这个地方
        }

        $this->app->response->setBody($return);
        
   }
    
    private function _init() {
        $this->rules = array(//查询
            'sub_id'=>array(
                'type'=>'multiId',
            ),
            'relation_id'=>array(
                'type'=>'multiId',
            ),
            'user_id'=>array(
                'type'=>'multiId',
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'update_time'=>array(
                'type'=>'string',
            ),
            'offset'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
             'limit'=>array(
               'type'=>'integer',
                 'default'=>100,
           ),
            'count' => array(   //当为1时获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
        );
        $this->params  = $this->query()->safe();

        $this->errors = $this->query()->getErrors();
        return TRUE;
    }

}