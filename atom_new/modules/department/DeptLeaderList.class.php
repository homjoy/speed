<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentLeaderInfo;

/**
 * 获取部门领导
 * @author haibinzhou
 * @date 2015-08-25
 */
class DeptLeaderList extends BaseModule{
	
    protected $params = array();
    protected $queryParams = array();
    protected $errors = array();

	public function run() {

		$this->_init();

		$list = DepartmentLeaderInfo::model()->getDataList($this->queryParams);

		if($list === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($list)) {
			$return = Response::gen_error(50002);
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
            'leader_id'=>array(
                'type'=>'multiId',
            ),
            'depart_id'=>array(
                'type'=>'multiId',
            ),
            'status'=>array(
                'type'=>'integer',
                'default' => 1,
            ),
        );
        $this->params = $this->query()->safe();
        $data = $this->request->GET;
        $this->queryParams = array_intersect_key($this->params,$data);

        $this->errors = $this->query()->getErrors();
    }

}