<?php
namespace Admin\Modules\Mail;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;

/**
 * 静态邮件列表处理接口
 *
 * @author guojiezhu@meilishuo.com
 * @since  2015-11-04
 */
class MailGroupAdd extends BaseModule
{

    protected $errors = null;
    private $params = null;
    private $page_size = 20;

    public function run()
    {
        $return = Response::gen_success(array());
        //获取部门树
        $department_info = Department::getInstance()->getDepart(array('all' => 1, 'status' => array(1)));
        $department_tree = $this->_filterDelDepart($department_info);
        $return['department_list'] = $department_tree;

        return $this->app->response->setBody($return);

    }


    /*
      * 过滤废弃的
      */
    protected function _filterDelDepart($department_info)
    {
        foreach ($department_info as $key => $item) {
            if ($item['memo'] == '废弃') {
                unset($department_info[$key]);
            }

        }
        return $department_info;
    }


}