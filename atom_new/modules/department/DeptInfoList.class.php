<?php
namespace Atom\Modules\Department;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfo;

/**
 * 获取部门信息 或 批量获取
 * @author hongzhou@meilishuo.com
 * @date 2015-8-17 上午10:56:25
 */
class DeptInfoList extends \Atom\Modules\Common\BaseModule{
    
    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        $queryParams = array();
        if(($this->params['interval']!='') && !empty($this->params['update_time'])){
            $queryParams['end_time'] = strtotime($this->params['update_time'])+$this->params['interval'];
            $queryParams['end_time'] = @date('Y-m-d H:i:s',$queryParams['end_time']);
        }
        if (!empty($this->params['update_time'])) {
            $queryParams['update_time']=@date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        if ($this->params['depart_id']!='') {
            $queryParams['depart_id'] = $this->params['depart_id'];
        }

        if ($this->params['parent_id']!=''){
            $queryParams['parent_id'] = $this->params['parent_id'];
        }

        if ($this->params['depart_name']!=''){
            $this->params['depart_name'] = trim($this->params['depart_name'] );
            $queryParams['depart_name'] = $this->params['depart_name'];
        }
        if ($this->params['depart_level']!='') {
            $queryParams['depart_level'] = $this->params['depart_level'];
        }
        if ($this->params['status']!='') {
            $queryParams['status'] = $this->params['status'];
        }
        if(empty($queryParams)){//以上为过滤条件 不要修改这个判断的地方
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        if ($this->params['count']!=''){
            $queryParams['count'] = $this->params['count'];
        }
        if ($this->params['match']!='') {
            $queryParams['match'] = $this->params['match'];
        }
        if ($this->params['all']!=''){
            $queryParams['all'] = $this->params['all'];
        }

        $ret = DepartmentInfo::model()->getDataList($queryParams, $this->params['offset'], $this->params['limit']);
        if ($ret === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success($ret);//这个地方
        }

        $this->app->response->setBody($return);
        
   }
    
    
    private function _init() {
        $this->rules = array(//查询
            'depart_id'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
            'depart_name'=>array(
                'type'=>'string',
            ),
            'depart_level'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
            'parent_id'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
            'status'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
          //  'child_id'=>array(
         //       'type'=>'string',
          //  ),
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
            'interval'=>array(
                'type'=>'integer',
            ),
            'match'=>array(
                'type'=>'string',
                 'default'=>'like'
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
    }

}