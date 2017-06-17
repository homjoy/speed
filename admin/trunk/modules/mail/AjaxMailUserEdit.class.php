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

/**
 * 废弃
 * 人员编辑
 *
 * @author guojiezhu@meilishuo.com
 * @since  2015-11-04
 */
class AjaxMailUserEdit extends BaseModule {

    protected $errors = null;
    private $params = null;
    public static $VIEW_SWITCH_JSON = true;

    public function run() {
        $this->_init();
        if (!empty($this->params['mail'])) {
            $mail_list = str_replace(array("\r\n", "\r", "\n", " ", " "), "#", $this->params['mail']);
            $mail_list = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $mail_list);
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

        //获取组的 email
        $mail_group_list = MailGroup::getInstance()->getMailGroupList(array('group_id' => $this->params['group_id']));
        if (!empty($mail_group_list)) {
            $u = $mail_group_list[0]['group_name'] . '@meilishuo.com';
        } else {
            $return = Response::gen_error('50001', '邮件组不存在');

            return $this->app->response->setBody($return);
        }

        //对数据库中邮箱数据进行处理
        $tmp_mail_list = $mail_list;
        //检查数据
        foreach ($tmp_mail_list as $mail_value) {
            $mail_value_list = explode('@', $mail_value);
            if (empty($mail_value_list[1]) && !in_array($mail_value_list[1], array('meilishuo.com', 'kf.meilishuo.com'))) {
                $return = Response::gen_error(Response::DS_DATA_NO_DATA, '邮箱格式错误,请检查：' . $mail_value, '邮箱格式错误,请检查：' . $mail_value);

                return $this->app->response->setBody($return);
            }
            if ($mail_value_list[1] == 'kf.meilishuo.com') {
                $search_user_params = array('mail' => $mail_value_list[0]);
                $out_user_info = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo($search_user_params);
                if (empty($out_user_info)) {
                    $is_mail_group = self::_checkIsMailGroup($mail_value_list[0]);
                    if (!$is_mail_group) {
                        $return = Response::gen_error(Response::DS_DATA_NO_DATA, '邮箱不存在,请检查：' . $mail_value, '邮箱不存在,请检查：' . $mail_value);

                        return $this->app->response->setBody($return);
                    }
                }
            } else {
                $check_params = array(
                    'mail' => $mail_value_list[0],
                );
                $mail_return = UserInfo::getInstance()->getUserInfo($check_params);
                if (empty($mail_return)) {
                    //去检查是否是邮件组
                    $is_mail_group = self::_checkIsMailGroup($mail_value_list[0]);
                    if (!$is_mail_group) {
                        $return = Response::gen_error(Response::DS_DATA_NO_DATA, '邮箱不存在,请检查：' . $mail_value, '邮箱不存在,请检查：' . $mail_value);

                        return $this->app->response->setBody($return);
                    }
                }
            }
        }
        //调用第三方接口发送数据
        $params = array(
            'act' => 'modMlist',
            'u' => $u
        );
        //要同步的邮箱的列表
        $data['forwardmaillist'] = implode(',', $tmp_mail_list);
        $return = MailGroupUser::getInstance()->pushMailUserList($params, $data);
        if (empty($return) || strtoupper($return['__STATUS__']) != 'OK') {
            $return = Response::gen_error(Response::DS_DATA_NO_DATA, '', '邮件系统创建失败');

            return $this->app->response->setBody($return);
        }

        //获取组下的所有用户
        $search_group_user = array(
            'all' => 1,
            'group_id' => $this->params['group_id'],
            'mail_suffix_type' => array(1, 2)
        );

        //获取数据
        $email_user_list = MailGroupUser::getInstance()->getMailUserList($search_group_user);
        //由于是这边进行控制用户，所以只需要比较可以使用的用户就可以了
        $allDbMails = array();
        if (!empty($email_user_list)) {
            foreach ($email_user_list as $k => $v) {
                $mail_suffix = UserOutsourcingInfo::getInstance()->get_mail_suffix($v['mail_suffix_type']);
                $allDbMails[] = trim($v['user_mail']) . $mail_suffix;
            }
        } else {
            $allDbMails = array();
        }

        //新的用户
        $new_mail_user = array_diff($mail_list, $allDbMails);
        //查询用户的信息
        $add_info = self::addNewMails($new_mail_user, $this->params['group_id']);
        if (!empty($new_mail_user)) {
            $log_data = $new_mail_user;
            $log_data['group_id'] = $this->params['group_id'];
            $log_info = array(
                'user_id' => $this->user['id'],
                'handle_id' => 99999,
                'operation_type' => 'add',
                'after_data' => json_encode($log_data),
                'handle_type' => 12
            );
            Log::getInstance()->createLogs($log_info);
        }
        //需要删除的用户
        $del_mails = array_diff($allDbMails, $mail_list);
        $del_info = self::delMails($del_mails, $this->params['group_id']);
        if (!empty($del_mails)) {
            $log_data = $del_mails;
            $log_data['group_id'] = $this->params['group_id'];
            $log_info = array(
                'user_id' => $this->user['id'],
                'handle_id' => 99999,
                'operation_type' => 'delete',
                'after_data' => json_encode($log_data),
                'handle_type' => 12
            );
            Log::getInstance()->createLogs($log_info);
        }

        if (empty($add_info) && empty($del_info)) {
            $return = Response::gen_success('数据更新成功');
        } else {
            is_array($del_info) || $del_info = array();
            is_array($add_info) || $add_info = array();
            $return_string = '删除失败：' . implode(',', $del_info) . ';更新失败：' . implode($add_info);
            $return = Response::gen_error('50001', $return_string);
        }

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
        $mail_group_list = MailGroup::getInstance()->getMailGroupList(array('email' => $mail,'strict' => 1));
        if (empty($mail_group_list)) {
            return false; //非邮件组
        } else {
            return current($mail_group_list); //是邮件组
        }
    }

    /**
     * 增加邮件组成员
     *
     * @param $newMails
     * @param $group_id
     *
     * @return bool
     */
    public static function addNewMails($new_mails, $group_id) {
        if (empty($new_mails) || empty($group_id)) {
            return false;
        }
        $search_user_params = array('status' => array(1, 2, 3));
        $add_return_array = array();
        foreach ($new_mails as $key => $value) {
            $mail_array = explode('@', $value);
            $suffix = '';
            $user_mail = '';
            if (!empty($mail_array)) {
                $suffix = $mail_array[1];
                $user_mail = $mail_array[0];
            }
            $mail_suffix_type = UserOutsourcingInfo::getInstance()->get_mail_suffix_type($suffix);

            //先查询一下，这个用户是否存在 当前组
            $search_group_user = array(
                'status' => array(0, 1),
                'group_id' => $group_id,
                'user_mail' => $user_mail,
                'mail_suffix_type' => $mail_suffix_type
            );
            $mail_group_user = MailGroupUser::getInstance()->getMailUserList($search_group_user);
            if (!empty($mail_group_user)) {
                $mail_group_user = current($mail_group_user);
                $update_exit_user = array(
                    'id' => $mail_group_user['id'],
                    'user_id' => $mail_group_user['user_id'],
                    'group_id' => $mail_group_user['group_id'],
                    'user_mail' => $mail_group_user['user_mail'],
                    'status' => 1,
                    'mail_suffix_type' => $mail_suffix_type
                );
                $res_data = MailGroupUser::getInstance()->updateMailGroupUser($update_exit_user);
                if (empty($res_data)) {
                    $add_return_array[] = $value;
                }
                continue;
            }

            //查询用户信息
            if ($mail_suffix_type == 1) {
                $search_user_params['mail'] = $user_mail;
                $user = UserInfo::getInstance()->getUserInfo($search_user_params);
                if (empty($user)) {
                    $mail_group_list = self::_checkIsMailGroup($user_mail);
                    if (!empty($mail_group_list)) {
                        $user[0] = array(
                            'mail'    => $mail_group_list['group_name'],
                            'user_id' => $mail_group_list['group_id'],
                        );
                    }

                }
                $user = current($user);
                $new = array(
                    'user_mail' => $user['mail'],
                    'user_id' => $user['user_id'],
                    'group_id' => $group_id,
                    'mail_suffix_type' => 1
                );
            } else {
                if ($mail_suffix_type == 2) {
                    $search_user_params['mail'] = $user_mail;
                    $user = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo($search_user_params);
                    if (empty($user)) {
                        $mail_group_list = self::_checkIsMailGroup($user_mail);
                        if (!empty($mail_group_list)) {
                            $user[0] = array(
                                'mail'    => $mail_group_list['group_name'],
                                'user_id' => $mail_group_list['group_id'],
                            );
                        }
                    }
                    $user = current($user);
                    $new = array(
                        'user_mail' => $user['mail'],
                        'user_id' => $user['out_user_id'],
                        'group_id' => $group_id,
                        'mail_suffix_type' => 2
                    );
                }
            }
            $res = MailGroupUser::getInstance()->createMailGroupUser($new);
            if (empty($res)) {
                $add_return_array[] = $value;
            }

        }

        return $add_return_array;
    }

    /**
     * 删除 邮件 组的成员 标示为 0
     *
     * @param $del_mail
     * @param $group_id
     *
     * @return bool
     */
    public static function delMails($del_mail, $group_id) {
        if (empty($del_mail)) {
            return false;
        }
        $all_return = array();
        $search_user_params['status'] = array(1,2,3);
        foreach ($del_mail as $key => $value) {
            $mail_array = explode('@', $value);
            $suffix = '';
            $user_mail = '';
            if (!empty($mail_array)) {
                $suffix = $mail_array[1];
                $user_mail = $mail_array[0];
            }
            $mail_suffix_type = UserOutsourcingInfo::getInstance()->get_mail_suffix_type($suffix);
            if ($mail_suffix_type == 1) {
                $search_user_params['mail'] = $user_mail;
                $user = UserInfo::getInstance()->getUserInfo($search_user_params);
                if (empty($user)) {
                    $mail_group_list = self::_checkIsMailGroup($user_mail);
                    if (!empty($mail_group_list)) {
                        $user[0] = array(
                            'mail'    => $mail_group_list['group_name'],
                            'user_id' => $mail_group_list['group_id'],
                        );
                    }
                }
                $user = current($user);
                $delete = array(
                    'user_mail' => $user['mail'],
                    'user_id' => $user['user_id'],
                    'group_id' => $group_id,
                    'mail_suffix_type' => 1,
                    'status' => 0,
                );
            } else {
                if ($mail_suffix_type == 2) {
                    $search_user_params['mail'] = $user_mail;
                    $user = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo($search_user_params);
                    if (empty($user)) {
                        $mail_group_list = self::_checkIsMailGroup($user_mail);
                        if (!empty($mail_group_list)) {
                            $user[0] = array(
                                'mail'    => $mail_group_list['group_name'],
                                'out_user_id' => $mail_group_list['group_id'],
                            );
                        }
                    }
                    $user = current($user);
                    $delete = array(
                        'user_mail' => $user['mail'],
                        'user_id' => $user['out_user_id'],
                        'group_id' => $group_id,
                        'mail_suffix_type' => 2,
                        'status' => 0,
                    );
                }
            }
            if(empty($delete['user_mail']) || empty($delete['user_id'])){
                return array();
            }
            $res = MailGroupUser::getInstance()->updateMailGroupUser($delete);
            if (empty($res)) {
                $all_return[] = $value;
            }
        }

        return $all_return;
    }

    function _init() {

        $this->rules = array(
            'group_id' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'integer',
            ),
            'mail' => array(
                'required' => true,
                'allowEmpty' => false,
                'type' => 'string'
            )
        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}
