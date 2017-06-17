<?php
namespace Atom\Modules\Department;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfoTemp;
use Atom\Package\Account\DepartmentRelationTemp;
use Atom\Package\Account\DepartmentSubTemp;

/**
 * 备份所有部门信息
 * @author minggeng@meilishuo.com
 * @date 2015-10-9
 */
class BackupAllDepartInfo extends BaseModule {


	public function run() {

        //清空temp表
        $result = DepartmentInfoTemp::model()->deleteAll();
        $result = DepartmentRelationTemp::model()->deleteAll();
        $result = DepartmentSubTemp::model()->deleteAll();

        $result = DepartmentInfoTemp::model()->insertAll();
        $result = DepartmentRelationTemp::model()->insertAll();
        $result = DepartmentSubTemp::model()->insertAll();

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)){
			$return = Response::gen_error(50003);
		}else {
			$return = Response::gen_success($result);
		}
		$this->app->response->setBody($return);
	}
	
}