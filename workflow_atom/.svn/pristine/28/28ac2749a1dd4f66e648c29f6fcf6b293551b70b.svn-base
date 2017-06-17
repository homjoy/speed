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

        //查询邮件组表
        $queryData = array(
            'group_name' => $emails,
            'status'=>array(1,0),
        );
        $records = MailGroup::getInstance()->getDataList($queryData, 1, 9999);
        //已有的数据
        $group_names = ArrayUtilities::my_array_column($records, 'group_name');

        //新邮件组
        $newGroups = array_diff($emails, $group_names);
        self::addNewGroup($newGroups);

        //已删除邮件组
        $oldGroups = array_diff($group_names, $emails);
        self::deleteOldGroup($oldGroups);

        //处理老数据
        foreach ($group_names as $key => $value) {
            if (empty($value['status'])) {
                $new = array('group_id' => $value['group_id'], 'status' => 1,);
                $res = MailGroup::getInstance()->update($new);

                if ($res) {
                    echo "delete group : {$value['group_name']} success \n";
                }else{
                    echo "delete group : {$value['group_name']} failed \n";
                }
            }
        }

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

}
