<?php

namespace Atom\Scripts\Mail;

use Libs\Util\ArrayUtilities;
use Atom\Package\Mail\MailGroup;
use Atom\Package\Mail\MailGroupUser;
use Atom\Package\Mail\MailGroupApi;

/**
 * 同步邮件组信息
 * Class MailGroupSync
 * @package Atom\Scripts\Mail
 */

class MailGroupSync extends \Frame\Script
{
    public function run()
    {
        global $start;
        $time = date('Y-m-d H:i:s');
        echo $time, "\n";

        //获取邮件服务器邮件组
        $emails = MailGroupApi::getInstance()->GetAllGroupList();
        if (empty($emails)) {
            return FALSE;
        }

        //查询所有邮件组表
        $queryData = array(
            'group_name' => $emails,
            'status'=>array(1,0),
        );
        $records = MailGroup::getInstance()->getDataList($queryData, 1, 9999);

        //筛选邮箱类别
        //所有邮箱,有效邮箱,无效邮箱
        $allDbMails = $availableDbMails = $disableMails = array();
        foreach ($records as $key => $value) {
            $allDbMails[] = $value['group_name'];
            if (empty($value['status'])) {
                $disableMails[] = $value['group_name'];
            }else{
                $availableDbMails[] = $value['group_name'];
            }
        }

        //新邮件组
        $newMails = array_diff($emails, $availableDbMails);

        //已存在用户
        $existMails = array_intersect($newMails, $disableMails);
        self::reNewGroup($existMails);

        //不存在用户
        $nonExistMails = array_diff($newMails, $disableMails);
        self::addNewGroup($nonExistMails);

        //已删除邮件组
        $oldMails = array_diff($availableDbMails, $emails);
        self::deleteOldGroup($oldMails);

        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //添加新邮件组
    public static function addNewGroup($newGroups = array()){
        if (empty($newGroups)) {
            return FALSE;
        }

        foreach ($newGroups as $key => $value) {
            $new = array('group_name' => $value);
            $res = MailGroup::getInstance()->insert($new);
            if ($res) {
                echo "add group : {$value} success \n";
            }else{
                echo "add group : {$value} failed \n";
            }
        }
        return TRUE;
    }

    //删除老邮件组
    public static function deleteOldGroup($oldGroups = array()){
        if (empty($oldGroups)) {
            return FALSE;
        }

        foreach ($oldGroups as $key => $value) {
            $delete = array('group_name' => $value, 'status' => 0,);
            $res = MailGroup::getInstance()->update($delete);

            if ($res) {
                echo "delete group : {$value} success \n";
            }else{
                echo "delete group : {$value} failed \n";
            }
        }
        return TRUE;
    }

    //更新老邮件组
    public static function reNewGroup($oldGroups = array()){
        if (empty($oldGroups)) {
            return FALSE;
        }

        foreach ($oldGroups as $key => $value) {
            $delete = array('group_name' => $value, 'status' => 1,);
            $res = MailGroup::getInstance()->update($delete);

            if ($res) {
                echo "renew group : {$value} success \n";
            }else{
                echo "renew group : {$value} failed \n";
            }
        }
        return TRUE;
    }
}
