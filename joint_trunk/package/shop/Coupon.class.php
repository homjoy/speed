<?php

namespace Joint\Package\Shop;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

/**
 * Class Coupon
 * @package Joint\Package\Shop
 * @author yongzhao
 * @desc 优惠券相关
 */
defined('ORDER_URL') || define("ORDER_URL", 'http://virusdoota.meilishuo.com/');

class Coupon extends \Joint\Package\Common\BasePackage {

//    const ORDER_URL = 'http://coupon.mlservice.meilishuo.com/';
    //const ORDER_URL = 'http://virusdoota.sichongcheng.rdlab.meilishuo.com/';
    private static $instance = null;
    private static $expireTime = 259200;

    private function __construct() {}

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function queryCoupon($params = array()){

        $api_params = array(
            'params' => $params,
        );
        $couponList = VirusCurlTool::getInstance()->post(ORDER_URL . 'coupon/query_coupon', $api_params);

        $couponList = json_decode($couponList,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Coupon:queryCoupon:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($couponList)) {
            $couponList = BaseMemcache::instance()->get($cacheKey);
            if (!empty($couponList)) {
                $couponList = json_decode($couponList, true);
                return $couponList['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($couponList), self::$expireTime);
            return $couponList['data'];
        }

        return FALSE;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function couponMeta($params = array()){

        $api_params = array();
        $api_params['basic_coupon_metas'][0] = array(
            'coupon_type' => $params['coupon_type'],
            'coupon_meta_id' => $params['coupon_meta_id']
        );
        $couponList = VirusCurlTool::getInstance()->post(ORDER_URL . 'coupon/batch_get_coupon_meta', $api_params);

        $couponList = json_decode($couponList,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Coupon:couponMeta:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($couponList)) {
            $couponList = BaseMemcache::instance()->get($cacheKey);
            if (!empty($couponList)) {
                $couponList = json_decode($couponList, true);
                return $couponList['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($couponList), self::$expireTime);
            return $couponList['data'];
        }

        return FALSE;

    }

    /**
     * @param array $params
     * @return bool
     */
    public function queryActiveCoupon($params = array()){
        $api_params = array(
            'params' => $params,
        );
        $couponList = VirusCurlTool::getInstance()->post(ORDER_URL . 'coupon/query_active_coupon_usage', $api_params);

        $couponList = json_decode($couponList,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Coupon:queryActiveCoupon:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($couponList)) {
            $couponList = BaseMemcache::instance()->get($cacheKey);
            if (!empty($couponList)) {
                $couponList = json_decode($couponList, true);
                return $couponList['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($couponList), self::$expireTime);
            return $couponList['data'];
        }

        return FALSE;
    }

    /**
     * 我的优惠券
     * @param array $params
     * @return bool
     */
    public function couponHomeList($params = array()){
        $couponList = VirusCurlTool::getInstance()->post(ORDER_URL . 'coupon/home_list', $params);

        $couponList = json_decode($couponList,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Coupon:couponHomeList:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($couponList)) {
            $couponList = BaseMemcache::instance()->get($cacheKey);
            if (!empty($couponList)) {
                $couponList = json_decode($couponList, true);
                return $couponList['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($couponList), self::$expireTime);
            return $couponList['data'];
        }

        return FALSE;
    }

    /**
     * 我的优惠券数量
     * @param array $params
     * @return bool
     */
    public function couponHomeListNum($params = array()){
        $uid = $params['uid'];
        unset($params['uid']);
        $num = VirusCurlTool::getInstance()->setHeader('Meilishuo:uid:'.$uid.';ip:127.0.0.1')->post(ORDER_URL . 'coupon/home_list_num', $params);

        $num = json_decode($num,true);
        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:Coupon:couponHomeListNum:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($num)) {
            $num = BaseMemcache::instance()->get($cacheKey);
            if (!empty($num)) {
                $num = json_decode($num, true);
                return $num['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($num), self::$expireTime);
            return $num['data'];
        }

        return FALSE;
    }
}
