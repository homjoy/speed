<?php
namespace Apicloud\Package\Account;

/**
 * UserWorkInfo
 * @package Apicloud\Package\Account
 * @author guojiezhu@meilishuo.com
 * @since 2016-03-28
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class UserWorkInfo extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 600;

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 查询
     */
    public static function getWorkInfo($params = array(),$is_cache = true) {
        //查询缓存
        $cacheKey = 'Account:UserWorkInfo:getWorkInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        if($is_cache) {
            $cacheData = BaseMemcache::instance()->get($cacheKey);
            if (!empty($cacheData)) {
                return $cacheData;
            }
        }

        //查询
        $result = self::getClient()->call('atom', 'account/get_work_info', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['error_code'] != 0) {
            return FALSE;
        }else{

            $data = $result['content']['data'];

            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);

            return $data;
        }
    }
    //修改个人信息
    public static function update($params = array()){
        $result = self::getClient()->call('atom', 'account/update_work_info', $params);

        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['error_code'] != 0) {
            return FALSE;
        }else{
            return $result['content']['data'];
        }
    }


} 
