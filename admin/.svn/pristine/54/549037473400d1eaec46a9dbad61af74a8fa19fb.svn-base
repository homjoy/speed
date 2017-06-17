<?php
namespace Admin\Modules\Mail;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Mail\MailGroupDepartRelation;
use Admin\Package\Mail\MailGroupUser;
use Admin\Package\Mail\MailGroup;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\UserOutsourcingInfo;
use Admin\Package\Log\Log;
use Admin\Package\Itserver\Itserver;

/**
 *  远程邮件组创建
 *
 * @author guojiezhu@meilishuo.com
 * @since  2015-11-04
 *  edit by guojiezhu add mail_group
 */
class AjaxMailGroupListAdd extends BaseModule
{

    protected $errors = null;
    private $params = null;
    public static $VIEW_SWITCH_JSON = true;

    public function run()
    {
        $this->_init();
        if (!empty($this->params['mail'])) {
            $mail_list = str_replace(array("\r\n", "\r", "\n", " ", " "), "#", $this->params['mail']);
            $mail_list = explode("#", $mail_list);
            $mail_list = array_unique($mail_list);
            foreach ($mail_list as $mail_key => &$mail_list_value) {
                $mail_list_value = trim($mail_list_value);
                if (empty($mail_list_value)) {
                    unset($mail_list[$mail_key]);
                }
            }
            unset($mail_list_value);
        } else {
            $mail_list = array();
        }

        // 校验是否已经存在
        $group_mail = explode('@', $this->params['group_name']);
        $group_str = current($group_mail);
        if (empty($group_str)) {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '请填写邮件列表');

            return $this->app->response->setBody($return);
        }
        $mail_group_list = MailGroup::getInstance()->getMailGroupList(array('email' => $group_str, 'strict' => 1, 'status' => array(0, 1)));
        if (!empty($mail_group_list)) {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '数据已经存在');

            return $this->app->response->setBody($return);
        }


        //对数据库中邮箱数据进行处理
        $tmp_mail_list = $mail_list;

        //检查邮件是否真实存在
        foreach ($tmp_mail_list as &$mail_value) {
            //过滤全角字符和空格
            $mail_value =preg_replace("/\s|　/","",$mail_value);
            $mail_value = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $mail_value);
            $mail_value_list = explode('@',$mail_value);
            if (empty($mail_value_list[1]) || !in_array($mail_value_list[1], MailGroup::$all_mail_suffix)) {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '邮箱格式错误,请检查：' . $mail_value, '邮箱格式错误,请检查：' . $mail_value);
                return $this->app->response->setBody($return);
            }
        }
        unset($mail_value);
        //调用第三方接口发送数据
        $params = array(
            'act' => 'createMlist',
            'u' => $group_str . '@meilishuo.com'
        );

        //要同步的邮箱的列表
        $data['forwardmaillist'] = implode(',', $tmp_mail_list);

        $return = MailGroupUser::getInstance()->pushMailUserList($params, $data);

        if (empty($return) || strtoupper($return['__STATUS__']) != 'OK') {
            $return = !empty($return['__MSG__'] ) ? $return['__MSG__'] :' IT接口异常';
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', $return);

            return $this->app->response->setBody($return);
        }
        //增加到邮件组
        $add_group_info = array(
            'group_name' => $group_str,
            'memo' => $this->params['memo'],
            'status' => 1
        );
        $add_group_id = MailGroup::getInstance()->createMailGroup($add_group_info);
        if (intval($add_group_id) > 0 && $this->params['depart_id'] > 0) {
            MailGroupDepartRelation::getInstance()->createMailGroupDepartRelation(array('group_id' => $add_group_id, 'depart_id' => $this->params['depart_id'], 'status' => 1));
        }
        $log_params = $params;
        $log_params['forwardmaillist'] = $mail_list;
        $log_info = array(
            'user_id' => $this->user['id'],
            'handle_id' => 99999,
            'operation_type' => 'add',
            'after_data' => json_encode($log_params),
            'handle_type' => 12
        );
        Log::getInstance()->createLogs($log_info);

        //创建失败
        $return = Response::gen_success('创建成功');
        return $this->app->response->setBody($return);


    }

    /**
     * 检查 mail 是否是邮件组
     *
     * @param $mail
     *
     * @return bool
     */
    protected static function _checkIsMailGroup($mail)
    {
        $mail_group_list = MailGroup::getInstance()->getMailGroupList(array('email' => $mail, 'strict' => 1));
        if (empty($mail_group_list)) {
            return false; //非邮件组
        } else {
            return current($mail_group_list); //是邮件组
        }
    }


    private function _init()
    {

        $this->rules = array(
            'group_name' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string',
            ),
            'mail' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string'
            ),
            'depart_id' => array(
                'type' => 'integer',
                'default' => 0
            ),
            'memo' => array(
                'type' => 'string',
                'default' => ''
            )

        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}