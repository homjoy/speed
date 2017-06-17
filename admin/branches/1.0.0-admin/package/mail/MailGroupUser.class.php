<?php

namespace Admin\Package\Mail;


/**
 * 邮件列表组数据
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class MailGroupUser extends \Admin\Package\Common\BasePackage {
    //mail的发送地址
    const EMAIL_URL ='http://yz.it.api01.meiliworks.com/apis/mail/core_api_v1.php';
    //const  EMAIL_URL='http://joint.meilishuo.com/test.php';
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
     * 推送邮件列表的相关数据
     * @param type $params
     * @data type array 需要post的数据
     * @return type
     * 
     */
    public function pushMailUserList($params = array(),$data= array()) {
        $params = array_merge($params,$data);
        $ret = self::getClient()->call('joint', 'itserver/email_group_manage', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;


    }

    /**
     * 获取加密数据
     * @param $act
     *
     * @return string
     */
    public  function checkKey($act){
        $ts = time();
        $sharedkey = "CoreMail2015";
        $nseckey = $sharedkey . $act . "$ts" ;
        $seckey = md5($nseckey);
        return $seckey;
    }
    
    /**
     * 获取 邮箱的列表
     * @param array $params
     *
     * @return bool
     */
    public  function getMailUserList($params = array()){
        $ret = self::getClient()->call('atom', 'mail/mail_group_user_search', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /*
     * 创建用户操作
     */
    public function createMailGroupUser($params = array()){
        $ret = self::getClient()->call('atom', 'mail/mail_group_user_create', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /*
     *  用户更新操作  （update and ）
     */
    public function updateMailGroupUser($params = array()){
        $ret = self::getClient()->call('atom', 'mail/mail_group_user_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /*
     *  删除用户组
     */
    public function deleteMailGroupUser($params = array()){
        $ret = self::getClient()->call('atom', 'mail/mail_group_user_delete', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
