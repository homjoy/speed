<?php

namespace Admin\Modules\Mail;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Log\Log;
use Admin\Package\Mail\MailGroupUser;
use Admin\Package\Mail\MailGroup;
/**
 * 邮件列表删除
 *
 * @author guojiezhu@meilishuo.com
 * @since  2016-03-08
 */
class AjaxMailGroupListDel extends BaseModule
{

    protected $errors = null;
    private $params = null;
    public static $VIEW_SWITCH_JSON = true;

    public function run()
    {
        $this->_init();
        //获取组的 email，去掉后缀
        if (empty($this->params['group_name'])) {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '邮件组不存在');

            return $this->app->response->setBody($return);
        }
        //对数据库中邮箱数据进行处理
        $mail_group_name = explode('@', $this->params['group_name']);
        $mail_group_prefix_name = current($mail_group_name);

        $params = array('act' => 'deleteMlist', 'u' => $mail_group_prefix_name . '@meilishuo.com');
//        //要同步的邮箱的列表
        $return = MailGroupUser::getInstance()->pushMailUserList($params, array());

        if (empty($return) || strtoupper($return['__STATUS__']) != 'OK') {
            $return = !empty($return['__MSG__'] ) ? $return['__MSG__'] :' IT接口异常';
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', $return);
        }
        //同步删除
        MailGroup::getInstance()->deleteMailGroup(array('email' => $mail_group_prefix_name,'status' =>0));

        //记录log

        $log_info = array(
            'user_id' => $this->user['id'],
            'handle_id' => 99999,
            'operation_type' => 'delete',
            'after_data' => json_encode($params),
            'handle_type' => 12);
        Log::getInstance()->createLogs($log_info);

        $return = Response::gen_success('删除成功');
        return $this->app->response->setBody($return);
    }

    private function _init()
    {

        $this->rules = array(
            'group_name' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string',),
        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}
