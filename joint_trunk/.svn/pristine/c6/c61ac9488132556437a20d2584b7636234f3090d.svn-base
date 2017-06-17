<?php

namespace Joint\Package\Order;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

/**
 * 
 * Class HigoOrderInfo
 * @package Joint\Package\Order
 * @author guojiezhu
 * @desc 支付相关的接口调用
 */
class HigoOrderInfo extends \Joint\Package\Common\BasePackage {

    //获取支付信息
    //const HIGO_ORDER_URL = 'http://10.7.0.37:8080/';
    //const HIGO_ORDER_URL = 'http://10.8.6.38:8080/';
    const HIGO_ORDER_URL = 'http://erp.lehe.com/';
    private static $instance = null;
    private static $expireTime = 259200;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取支付相关的接口
     * @param type $params
     * @return boolean
     * http://192.168.190.2:9010/higo_api/order/order_list_kf?buyer_account_mobile=15810779839&ctime_start=2015-06-17%2000:00:00&ctime_end=2015-06-18%2000:00:00&page_curr=1&version=2
     */
    public function getOrderInfo($params = array()) {
        $debug = isset($params['debug']) ? $params['debug'] : '';
        unset($params['debug']);
        if (empty($params)) {
            return FALSE;
        }
        //单笔支付单查询请求

        //$search_params = $params;
        //$query = http_build_query($search_params);
        $url_pay = self::HIGO_ORDER_URL . 'higo_api/order/order_list_kf';
      
        $orderInfo = VirusCurlTool::getInstance()->get($url_pay,$params);
        if($debug){ var_dump($orderInfo); }
        $orderInfo = json_decode($orderInfo,true);
        //从接口时时的去读 ，如果读取不到，取混存数据   
        $cacheKey = 'Joint:HigoOrderInfo:getOrderInfo:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($orderInfo)) {
            $orderInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($orderInfo)) {
                return json_decode($orderInfo, true);
            }   
        }else{
            //生成缓存   
            BaseMemcache::instance()->set($cacheKey, json_encode($orderInfo), self::$expireTime);
            return $orderInfo;
        }
                
        return FALSE;
    }
    
    
    

}
