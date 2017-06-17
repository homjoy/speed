<?php
namespace Apicloud\Package\Account;

/**
 * MDAvtar
 * @package Apicloud\Package\Account
 * @author hepang@meilishuo.com
 * @since 2015-08-11
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;

class MDAvtar extends \Apicloud\Package\Common\BasePackage{

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

    /**
     * 获取md头像
     */
    public static function getMDAvtar($params = array()) {
        //查询缓存
        $cacheKey = MEM_PREFIX.'Account:MDAvtar:getMDAvtar:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'account/get_md_avatar', $params);

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

} 
