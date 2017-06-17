<?php

namespace Admin\Package\Core;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
/**
 * Prompt
 * @package Admin\Package\Core
 * @author hongzhou@meilishuo.com
 * @since 2015-01-18
 */

class Prompt  extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }

    public  function getNoticeInfo($params = array()){
        $ret = self::getClient()->call('atom', 'notice/notice_info_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    public  function createNoticeInfo($params = array()) {

        $ret = self::getClient()->call('atom', 'notice/notice_info_create', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;

    }
    public  function updateNoticeInfo($params = array()){

        $ret = self::getClient()->call('atom', 'notice/notice_info_update', $params);

        if(isset($ret['content']['error_code'])&&$ret['content']['error_code']==50012){
            return array();
        }elseif(isset($ret['content']['data'])){
            return array('msg'=>'操作成功');
        }else{
            $ret = self::parseRemoteData($ret);
        }
        return $ret;
    }

}
