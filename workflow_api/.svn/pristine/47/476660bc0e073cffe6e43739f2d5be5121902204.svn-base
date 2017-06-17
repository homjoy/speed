<?php

namespace Atom\Scripts\Mail;

use Libs\Util\ArrayUtilities;
use Atom\Package\Mail\MailGroup;
use Atom\Package\Mail\MailGroupUser;
use Atom\Package\Mail\MailGroupApi;
use Atom\Package\User\UserInfo;

/**
 * 同步邮件组员工信息
 * Class MailGroupUserSync
 * @package Atom\Scripts\Mail
 */

class MailGroupUserSync extends \Frame\Script
{
    public function run()
    {
        //查询邮件组表
        $queryData = array(
            'status'=>array(1),
        );
        $records = MailGroup::getInstance()->getDataList($queryData, 1, 9999);

        //已有的数据
        $group_names = ArrayUtilities::my_array_column($records, 'group_name');

        foreach ($records as $key => $value) {
            //查询数据库
            $queryData = array(
                'group_id'  => $value['group_id'],
                'status'    => array(1,0),
            );
            $dbUsers = MailGroupUser::getInstance()->getDataList($queryData, 1, 9999);
            $dbMails = ArrayUtilities::my_array_column($dbUsers, 'user_mail');

            //查询远程邮箱接口
            $remoteUsers = MailGroupApi::getInstance()->GetMemberByGroup($value['group_name']);

            //处理
            //新用户成员
            $newMails = array_diff($remoteUsers, $dbMails);
            self::addNewMails($newMails, $value['group_id']);

            //已删除用户成员
            $oldMails = array_diff($dbMails, $remoteUsers);
            self::deleteOldMails($oldMails, $value['group_id']);

            //处理老数据
            foreach ($dbMails as $k => $v) {
                if (empty($v['status'])) {
                    $reNew = array('id' => $v['id'], 'status' => 1,);
                    $res = MailGroupUser::getInstance()->update($reNew);

                    if ($res) {
                        echo "renew user : {$value['id']} to group : {$value['group_id']} success \n";
                    }else{
                        echo "renew user : {$value['id']} to group : {$value['group_id']} failed \n";
                    }
                }
            }

        }

        global $start;
        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //添加新邮件组
    public static function addNewMails($newMails = array(), $group_id = 0){

        if (empty($newMails) || empty($group_id)) {
            return FALSE;
        }

        foreach ($newMails as $key => $value) {

            //查询用户信息
            $user = UserInfo::getInstance()->getByMail($value);
            if (empty($user)) {
                continue;
            }
            $user = current($user);

            $new = array('user_mail' => $user['mail'], 'user_id' => $user['user_id'], 'group_id' => $group_id,);
            $res = MailGroupUser::getInstance()->insert($new);

            if ($res) {
                echo "add user : {$value} to group : {$group_id} success \n";
            }else{
                echo "add user : {$value} to group : {$group_id} failed \n";
            }
        }
        return TRUE;
    }

    //删除老邮件组
    public static function deleteOldMails($oldMails = array(), $group_id = 0){
        if (empty($oldMails)) {
            return FALSE;
        }

        foreach ($oldMails as $key => $value) {

            //查询用户信息
            $user = UserInfo::getInstance()->getByMail($value);
            if (empty($user)) {
                continue;
            }
            $user = current($user);

            $delete = array('group_id' => $group_id, 'user_id' => $user['user_id'], 'status' => 0,);
            $res = MailGroupUser::getInstance()->update($delete);

            if ($res) {
                echo "delete user : {$value} from group : {$group_id} success \n";
            }else{
                echo "delete user : {$value} from group : {$group_id} failed \n";
            }
        }
        return TRUE;
    }

}
