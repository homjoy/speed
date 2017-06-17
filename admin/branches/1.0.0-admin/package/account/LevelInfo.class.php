<?php

namespace Admin\Package\Account;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * 获取用户信息
 * @package Admin\Package\Account\LevelInfo
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class LevelInfo extends \Admin\Package\Common\BasePackage {
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
    public  function getLevelInfo($params = array()){
        $ret = self::getClient()->call('atom', 'account/user_job_level_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


}
