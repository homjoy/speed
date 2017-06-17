<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户信息
 * @package Admin\Package\Account\TitleInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class TitleInfo extends \Admin\Package\Common\BasePackage {
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
     * 读取用户职位
     */
    public  function getTitleInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/user_job_title_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     *用户创建职位
     */
    public  function createTitleInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/create_user_job_title', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

}
