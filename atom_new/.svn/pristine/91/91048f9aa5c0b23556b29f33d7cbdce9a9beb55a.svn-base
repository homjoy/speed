<?php
namespace Atom\Package\Mail;

/*
 * 获取邮件组信息
 * @auth hepang@meilishuo.com
 * @2015-07-03
 */ 

use Libs\Sphinx\curl;

class MailGroupApi{

    private static $instance;
    private static $curl_obj;

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self();
            self::$curl_obj = new curl();
        }
        return self::$instance;
    }

    public function __construct() {
        //defined('MAIL_GROUP_API') || define("MAIL_GROUP_API", 'http://172.16.0.123/apis/aliasinfo.php');
        defined('MAIL_GROUP_API') || define("MAIL_GROUP_API", 'http://yz.it.api01.meiliworks.com/apis/mail/aliasinfo.php');
    }

    /**
     * 查询所有的邮件组
     * @return array
     */
    public static function GetAllGroupList()
    {
        $result = self::requestAllGroupList();

        $return = array();
        if(!empty($result) && is_array($result))
        {
            foreach ($result as $value)
            {
                $value = strtok($value, '@');
                $value = strtolower($value);
                $return[] = $value;
            }
        }

        return array_unique($return);
    }

    /**
     * 查询所有的邮件组
     * @return array
     * curl http://172.16.0.123/apis/aliasinfo.php?act=GetGroupList
     */
    public static function requestAllGroupList()
    {
        $params     = array('act'=>'GetGroupList',);
        $requestUrl = MAIL_GROUP_API . '?' . http_build_query($params);

        return self::requestAndReturn($requestUrl);
    }

    /**
     * 查询指定邮件组的用户
     * @param mail group
     * @return array
     */
    public static function GetMemberByGroup($group_mail = '')
    {
        $result = self::requestMemberByGroup($group_mail);

        $userData = array();
        if(!empty($result) && !empty($result["__FOUND__"]))
        {
            foreach ($result['data'] as $value)
            {
                $value = strtok($value, '@');
                $value = strtolower($value);
                $userData[] = $value;
            }
        }
        return array_unique($userData);
    }

    /**
     *  返回带后缀的邮件组用户
     * @param mail group
     * @return array
     */
    public static function GetSuffixMemberByGroup($group_mail = '')
    {
        $result = self::requestMemberByGroup($group_mail);

        $userData = array();
        if(!empty($result) && !empty($result["__FOUND__"]))
        {
            foreach ($result['data'] as $value)
            {

                $userData[] = trim($value);
            }
        }
        return array_unique($userData);
    }


    /**
     * 查询指定邮件组的用户
     * @return array
     * curl http://172.16.0.123/apis/aliasinfo.php?act=GetMemberByGroup&vgrp=sop@meilishuo.com
     */
    public static function requestMemberByGroup($group_mail = '')
    {
        $group_mail = $group_mail.'@meilishuo.com';
        $params     = array('act'=>'GetMemberByGroup', 'vgrp'=>$group_mail,);
        $requestUrl = MAIL_GROUP_API . '?' . http_build_query($params);

        return self::requestAndReturn($requestUrl);
    }

    /**
     * 解析curl返回数据
     * @param array
     * @return array
     */
    public static function requestAndReturn($url = '')
    {
        if (empty($url)) {
            return FALSE;
        }

        $ret = self::$curl_obj->get($url);

        if(empty($ret) || !isset($ret['body']) || empty($ret['body'])) {
            return FALSE;
        }
        $body = json_decode($ret['body'], TRUE);

        if(json_last_error() != JSON_ERROR_NONE) {
            return FALSE;
        }

        return $body;
    }

}
