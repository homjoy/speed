<?php
namespace Apicloud\Package\Core;

/**
 * 获取配置数据表中的数据
 * @package Apicloud\Package\Core
 * @author haibinzhou@meilishuo.com
 * @since 2015-09-10
 */

use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;
use Frame\Speed\Lib\Api;

class Config extends \Apicloud\Package\Common\BasePackage{

    private static $instance = null;
    private static $expireTime = 300;

	private function __construct() {}

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取配置表中的值
     */
    public static function getValue($params = array()) {

        //查询缓存
        $cacheKey = 'Config:getValue:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
			return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'core/config_get_value', $params);

        if($result['httpcode'] != 200) {
            return '';
        } elseif(empty($result['content']) || $result['content']['code'] != 200) {
            return '';
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $result['content']['data'], self::$expireTime);

            return $result['content']['data'];
        }
    }

    /**
     *
     *  获取子类
     *
     */
    public static function getChild($params = array()){
        //查询缓存
          $cacheKey = 'Config:getChild:';
          $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
          $cacheData = BaseMemcache::instance()->get($cacheKey);
          if (!empty($cacheData)) {
              return $cacheData;
          }

        //查询
        $result = self::getClient()->call('atom', 'core/config_get_child', $params);

        //生成缓存
        BaseMemcache::instance()->set($cacheKey, $result['content']['data'], self::$expireTime);

        return $result['content']['data'];
    }

    /**
     *
     *  获取配置列表list
     *
     */
    public static function getList($params = array()){
        //查询缓存
        $cacheKey = 'Config:getList:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'core/config_list', $params);

        //生成缓存
        BaseMemcache::instance()->set($cacheKey, $result['content']['data'], self::$expireTime);

        return $result['content']['data'];
    }


} 
