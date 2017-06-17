<?php
namespace Admin\Modules\Mail;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Mail\MailGroupUser;
use Admin\Package\Mail\MailGroup;
use Admin\Package\Account\UserOutsourcingInfo;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
use Admin\Package\Itserver\Itserver;
use Admin\Package\Mail\MailGroupDepartRelation as MDRelation;

/**
 *  邮件组成员的处理
 *
 * @author guojiezhu@meilishuo.com
 * @since  2015-11-04
 */
class AjaxMailGroupEdit extends BaseModule
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

        //获取邮件组的 email
        if (empty($this->params['group_name'])) {
            $return = Response::gen_error('50001', '邮件组错误');
            return $this->app->response->setBody($return);
        }
        //邮件组的
        $mail_group_name = explode('@', $this->params['group_name']);
        $mail_group_prefix_name = current($mail_group_name);
        $u = $mail_group_prefix_name . '@meilishuo.com';

        //对数据库中邮箱数据进行处理
        $tmp_mail_list = $mail_list;
        //检查数据
        foreach ($tmp_mail_list as &$mail_value) {
            $mail_value =preg_replace("/\s|　/","",$mail_value);
            $mail_value = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $mail_value);
            $mail_value_list = explode('@', $mail_value);
            //限制后缀
            if (empty($mail_value_list[1]) || !in_array($mail_value_list[1], MailGroup::$all_mail_suffix)) {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '邮箱后缀格式错误,请检查：' . $mail_value, '邮箱后缀格式错误,请检查：' . $mail_value);
                return $this->app->response->setBody($return);
            }

        }
        unset($mail_value);
        //调用第三方接口发送数据
        $params = array(
            'act' => 'modMlist',
            'u' => $u
        );

        //要同步的邮箱的列表
        $data['forwardmaillist'] = implode(',', $tmp_mail_list);
        $return = MailGroupUser::getInstance()->pushMailUserList($params, $data);
        if (empty($return) || strtoupper($return['__STATUS__']) != 'OK') {
            $return = !empty($return['__MSG__'] ) ? $return['__MSG__'] :' IT接口异常';
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', $return);
            return $this->app->response->setBody($return);
        }
        //创建成功,同步将数据,更新到relation
        if(!empty($this->params['depart_id'])){
            $md_params = array(
                'group_id' => $this->params['group_id'],
                //'depart_id' => $this->params['depart_id'],
                'status'   => array(0,1)
            );
            $depart_group_info = MDRelation::getInstance()->getMailGroupDepartRelation($md_params);
            //数据插入
            if(empty($depart_group_info)){
                MDRelation::getInstance()->createMailGroupDepartRelation(array(
                    'group_id' => $this->params['group_id'],
                    'depart_id' => $this->params['depart_id'],
                    'status' => 1));
            }else{ //更新
                $depart_group_data = current($depart_group_info);
                if($depart_group_data['status'] == 0 || ($depart_group_data['depart_id'] != $this->params['depart_id'] )) {
                    MDRelation::getInstance()->updateMailGroupDepartRelation(array(
                        'id' => $depart_group_data['id'],
                        'group_id' => $this->params['group_id'],
                        'depart_id' => $this->params['depart_id'],
                        'status' => 1));
                }
            }
        }
        //如果备注不为空,则修改备注
        if(!empty($this->params['memo'])){
            MailGroup::getInstance()->updateMailGroup(array(
                'email'=> $mail_group_prefix_name,'memo' => $this->params['memo'],'status' =>1
            ));
        }
        $return = array();
        $log_data = $params;
        $log_data['group_name'] = $this->params['group_name'];
        $log_info = array(
            'user_id'        => $this->user['id'],
            'handle_id'      => 99999,
            'operation_type' => 'add',
            'after_data'     => json_encode($log_data),
            'handle_type'    => 12
        );
        Log::getInstance()->createLogs($log_info);

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
                'type' => 'integer'
            ),
            'group_id' => array(
                'type' => 'integer'
            ),
            'memo' => array(
                'type' => 'string'
            )

        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}