<?php

namespace Atom\Scripts\Mail;

use Libs\Util\ArrayUtilities;
use Atom\Package\Mail\MailGroup;
use Atom\Package\Mail\MailGroupUser;
use Atom\Package\Mail\MailGroupApi;
use Atom\Package\User\UserInfo;
use Atom\Package\Account\UserOutsourcingInfo;

/**
 * 同步邮件组员工信息
 * Class MailGroupUserSync
 * @package Atom\Scripts\Mail
 * @edit by guojiezhu@meilishuo.com @ 2016-03-09 for 兼容蘑菇街,邮件组账号
 *
 */
class MailGroupUserSync extends \Frame\Script
{
    public function run()
    {
        //查询邮件组表
        $queryData = array(
            'status' => array(1),
        );
        $records = MailGroup::getInstance()->getDataList($queryData, 1, 9999);

        //已有的数据
        //$group_names = ArrayUtilities::my_array_column($records, 'group_name');

        foreach ($records as $key => $value) {


            //查询所有记录
            $queryData = array(
                'group_id' => $value['group_id'],
                'status' => array(1, 0),
            );
            $allDbUsers = MailGroupUser::getInstance()->getDataList($queryData, 1, 9999);

            //筛选邮箱类别
            //所有邮箱,有效邮箱,无效邮箱
            $allDbMails = $availableDbMails = $disableMails = array();
            foreach ($allDbUsers as $k => $v) {
                $allDbMails[] = $v['user_mail'];
                if (empty($v['status'])) {
                    $disableMails[] = $v['user_mail'] . self::_getSuffix($v['mail_suffix_type']);
                } else {
                    $availableDbMails[] = $v['user_mail'] .  self::_getSuffix($v['mail_suffix_type']);
                }
            }

            //查询远程邮箱接口
            $remoteUsers = MailGroupApi::getInstance()->GetSuffixMemberByGroup($value['group_name']);


            //处理
            //---------------------------------------------------------
            //新用户成员
            //---------------------------------------------------------
            $newMails = array_diff($remoteUsers, $availableDbMails);

            //已存在用户
            $existMails = array_intersect($newMails, $disableMails);
            self::reNewMails($existMails, $value['group_id']);

            //不存在用户
            $nonExistMails = array_diff($newMails, $disableMails);
            self::addNewMails($nonExistMails, $value['group_id']);

            //---------------------------------------------------------
            //已删除用户成员
            //---------------------------------------------------------
            $oldMails = array_diff($availableDbMails, $remoteUsers);
            self::deleteOldMails($oldMails, $value['group_id']);
        }
        global  $start;
        $end = microtime(true);
        $total = $end - $start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //根据suffix_type 获取后缀 @meilishuo.com
    public  function _getSuffix($suffix_type)
    {
        $suffix_type = intval($suffix_type);
        switch ($suffix_type) {
            case 1:
                return '@meilishuo.com';
            case 2:
                return '@kf.meilishuo.com';
            default:
                return '@meilishuo.com';
        }
    }


    //根据mail 地址返回用户的信息
    public static function _getUserInfo($mail_info)
    {
        $user_mail_array = explode('@', $mail_info);
        $mail_prefix = current($user_mail_array);
        $mail_suffix = array_pop($user_mail_array);
        //美丽说用户
        if ($mail_suffix == 'meilishuo.com') { //美丽说的用户
            $user = UserInfo::getInstance()->getByMail($mail_prefix);
            if (!empty($user)) {
                $user = current($user);
                $reNew = array('mail' => $mail_prefix,'user_id' => $user['user_id'], 'mail_suffix_type' => 1);
                return $reNew;
            }
        }
        //外包 用户
        if ($mail_suffix == 'kf.meilishuo.com') {
            $search_user_params = array('mail' => $user_mail_array,'status' => array(1,2,3));
            $out_user_info = UserOutsourcingInfo::model()->getDataList($search_user_params);
            if (!empty($out_user_info)) {
                $out_user_info = current($out_user_info);
                $reNew = array('mail' => $mail_prefix,'user_id' => $out_user_info['out_user_id'], 'mail_suffix_type' => 2);
                return $reNew;

            }
        }
        //查询是否是群组
        $mail_group = self::_checkIsMailGroup($mail_prefix);

        if (!empty($mail_group)) {
            $reNew = array('mail' => $mail_prefix,'user_id' => $mail_group['group_id'], 'mail_suffix_type' => 1);
            return $reNew;


        }
        return array();
    }

    //更新邮件组
    public static function reNewMails($newMails = array(), $group_id = 0)
    {

        if (empty($newMails) || empty($group_id)) {
            return FALSE;
        }

        foreach ($newMails as $key => $value) {
            //根据邮箱获取用户信息
            $user_info = self::_getUserInfo($value);
            if (!empty($user_info)) {
                $reNew = array('user_id' => $user_info['user_id'], 'group_id' => $group_id, 'status' => 1, 'mail_suffix_type' => $user_info['mail_suffix_type']);
                $res = MailGroupUser::getInstance()->update($reNew);
            } else {
                $res = false;
            }

            if ($res) {
                echo "renew user : {$value} to group : {$group_id} success \n";
            } else {
                echo "renew user : {$value} to group : {$group_id} failed \n";
            }
        }
        return TRUE;
    }

    //添加新邮件组
    public static function addNewMails($newMails = array(), $group_id = 0)
    {

        if (empty($newMails) || empty($group_id)) {
            return FALSE;
        }

        foreach ($newMails as $key => $value) {
            //解析出数据
            $user_info = self::_getUserInfo($value);
            if (!empty($user_info)) {
                $new = array('user_mail' => $user_info['mail'], 'user_id' => $user_info['user_id'], 'group_id' => $group_id, 'mail_suffix_type' => $user_info['mail_suffix_type']);
                $res = MailGroupUser::getInstance()->insert($new);
            } else {
                $res = false;
            }
            if ($res) {
                echo "add user : {$value} to group : {$group_id} success \n";
            } else {
                echo "add user : {$value} to group : {$group_id} failed \n";
            }
        }
        return TRUE;
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
        $mail_group_list = MailGroup::getInstance()->getDataList(array('group_name' => $mail, 'strict' => 1));
        if (empty($mail_group_list)) {
            return false; //非邮件组
        } else {
            return current($mail_group_list); //是邮件组
        }
    }

    //删除老邮件组
    public static function deleteOldMails($oldMails = array(), $group_id = 0)
    {
        if (empty($oldMails)) {
            return FALSE;
        }

        foreach ($oldMails as $key => $value) {

            //解析出数据
            $user_info = self::_getUserInfo($value);

            if (!empty($user_info)) {
                $delete = array('group_id' => $group_id, 'user_id' => $user_info['user_id'], 'status' => 0, 'mail_suffix_type' => $user_info['mail_suffix_type']);
                $res = MailGroupUser::getInstance()->update($delete);
            }else{
                $res = false;
            }

            if ($res) {
                echo "delete user : {$value} from group : {$group_id} success \n";
            } else {
                echo "delete user : {$value} from group : {$group_id} failed \n";
            }
        }
        return TRUE;
    }

}
