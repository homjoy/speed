<?php
namespace Admin\Modules\Message_send;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Department\Department;

/**
 * 获取部门tree
 * Class AjaxMessageSend
 *
 * @package Admin\Modules\Messagesend
 */
class AjaxDepartTree extends BaseModule
{

	protected $errors = null;
	public static $VIEW_SWITCH_JSON = true;
	//返回数据


	public function run()
	{

		$all_depart = Department::getInstance()->getDepart(array('all' => 1,'status' =>1));

	}



}