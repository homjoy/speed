<?php

namespace Admin\Package\Mail;


/**
 * mail 的通用方法
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class MailGroupDepartRelation extends \Admin\Package\Common\BasePackage {
    //mail的发送地址
    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }



    /**
     * 获取邮件组的列表
     * @param array $params
     *
     * @return bool
     */
    public  function getMailGroupDepartRelation($params = array()){

        $ret = self::getClient()->call('atom', 'mail/get_mail_group_depart_relation', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 创建邮件组和部门的对应关系
     * @param array $params
     *
     * @return bool
     */
    public  function createMailGroupDepartRelation($params = array()){

        $ret = self::getClient()->call('atom', 'mail/create_mail_group_depart_relation', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     *  修改邮件组和部门的对应关系
     * @param array $params
     *
     * @return bool
     */
    public  function updateMailGroupDepartRelation($params = array() ){

        $ret = self::getClient()->call('atom', 'mail/update_mail_group_depart_relation', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }



}
