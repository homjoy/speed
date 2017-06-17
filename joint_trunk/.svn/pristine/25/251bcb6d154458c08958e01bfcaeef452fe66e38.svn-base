<?php

namespace Joint\Package\Order;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

/**
 * 
 * Class VirusOrderInfo
 * @package Joint\Package\Order
 * @author guojiezhu
 * @desc 与订单相关的接口，调用Virus 的接口
 */
defined('VIRUS_URL') || define("VIRUS_URL", 'http://virusdoota.meilishuo.com/');

class VirusOrderInfo extends \Joint\Package\Common\BasePackage {

    private static $instance = null;
    private static $expireTime = 600;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * redime 
     * 获取 退款详情 
     * @param type $params
     * @return boolean
     */
    public function getRefundServiceList($params= array()) {
        if (empty($params['order_id'])) {
            return FALSE;
        }
        //退款的url
        $urlRefundUrl = VIRUS_URL."refund/refund_service_list";
        $refundInfo = VirusCurlTool::getInstance()->setHeader('Meilishuo:uid:49;ip:192.168.1.1')->post($urlRefundUrl,$params);
        $refundInfo = json_decode($refundInfo,true);
        //从接口时时的去读 ，如果读取不到，取混存数据   
        $cacheKey = 'Joint:VirusOrderInfo:getRefundServiceList:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($refundInfo)) {
            $refundInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($refundInfo)) {
               $returnInfo =  json_decode($refundInfo, true);
               return $returnInfo['data'];
            }   
        }else{
            //生成缓存   
            BaseMemcache::instance()->set($cacheKey, json_encode($refundInfo), self::$expireTime);
            return $refundInfo['data'];
        }
                
        return FALSE;
    }
    
    /**
     * 获取仲裁信息
     * 
     */
    public function getAppealList($params = array() ){
        if (empty($params['order_id'])) {
            return FALSE;
        }
        //退款的url
        $urlAppealUrl = VIRUS_URL."appeal/appeal_list";
        $appealInfo = VirusCurlTool::getInstance()->setHeader('Meilishuo:uid:49;ip:192.168.1.1')->get($urlAppealUrl,$params);
        $appealInfo = json_decode($appealInfo,true);
        //从接口时时的去读 ，如果读取不到，取混存数据   
        $cacheKey = 'Joint:VirusOrderInfo:getAppealList:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($appealInfo)) {
            $appealInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($appealInfo)) {
               $appealArray =  json_decode($appealInfo, true);
               return $appealArray['data'];
            }   
        }else{
            //生成缓存   
            BaseMemcache::instance()->set($cacheKey, json_encode($appealInfo), self::$expireTime);
            return $appealInfo['data'];
        }
                
        return FALSE;
    }
    
    /**
     *  获取仲裁的详细信息
     * @param type $params
     * @return type
     */
    public function getAppealDeatil($params = array()){
        if(empty($params['refund_id'])){
            return FALSE;
        }
        $urlAppealDetail = VIRUS_URL."appeal/appeal_detail";
        $appealDetail = VirusCurlTool::getInstance()->setHeader('Meilishuo:uid:49;ip:192.168.1.1')->post($urlAppealDetail, $params);
        $appealDetail = json_decode($appealDetail,true);
		//从接口时时的去读 ，如果读取不到，取缓存数据   
        $cacheKey = 'Joint:VirusOrderInfo:getAppealDeatil:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($appealDetail)) {
            $appealDetail = BaseMemcache::instance()->get($cacheKey);
            if (!empty($appealDetail)) {
               return  json_decode($appealDetail, true);
            }   
        }else{
            //生成缓存   
            BaseMemcache::instance()->set($cacheKey, json_encode($appealDetail), self::$expireTime);
            return $appealDetail;
        }
        return FALSE;
        
    }
    
    

}

?>