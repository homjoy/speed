<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelationTemp;
use Atom\Package\Migrate\Crab;
use Frame\Speed\Exception\ParameterException;
use Libs\Util\ArrayUtilities;
/**
 * 获取部门领导
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-24
 */
class DepartRelationTempList extends BaseModule{

    protected $params = array();
    protected $errors = array();

    public function run() {

        $this->_init();

        //新库数据
        $list = DepartmentRelationTemp::model()->getDataList($this->params,$this->params['offset'],$this->params['limit']);
        $list = ArrayUtilities::hashByKey($list,'depart_id');

        if($list === FALSE) {
            $return = Response::gen_error(10004);
        }else {
            $return = Response::gen_success(Format::outputData($list));
        }

        $this->app->response->setBody($return);
    }


    private function _init() {
        $this->rules = array(
            'user_id'=>array(
                'type'=>'multiId',
            ),
            'relation_id'=>array(
                'type'=>'multiId',
            ),
            'parent_relation_id'=>array(
                'type'=>'multiId',
            ),
            'depart_id'=>array(
                'type'=>'multiId',
            ),
            'role_id'=>array(
                'type'=>'multiId',
            ),
            'update_time'=>array(
                'type'=>'string',
            ),
            'status'=>array(
                'type'=>'multiId',
                'default' => 1,
            ),
            'is_virtual'=>array(
                'type'=>'multiId',
                'default' => 0,
            ),
            'offset' => array(
                'type' => 'integer',
                'default'=>0,
            ),
            'limit' => array(
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

        $this->params = $this->query()->safe();

        $this->errors = $this->query()->getErrors();
    }

}