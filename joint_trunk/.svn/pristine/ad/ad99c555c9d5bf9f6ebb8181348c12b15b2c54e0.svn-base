<?php

namespace Joint\Package\Order;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

/**
 * 
 * Class OrderWeixin
 * @package Joint\Package\Order
 * @author guojiezhu
 * @desc 支付相关的接口调用
 */
class OrderPay extends \Joint\Package\Common\BasePackage {

    //获取支付信息
    const PAY_KEY = '953b953831b73d2b635c86108fb36b80';
    const MPAY_URL = 'http://mpay.meilishuo.com/';
    //const MPAY_URL = 'http://mpay.renjiezhang.rdlab.meilishuo.com/';

    //支付的信息
    protected $pay_mode_map = array(
        'CCARD' => 'PC信用卡',
        'DCARD' => 'PC借记卡',
        'GWCARD' => 'PC平台',
        'PUBACCOUNT' => '微信公账',
        'QCCARD' => 'PC端快捷支付信用卡',
        'QDCARD' => 'PC端快捷支付借记卡',
        'QUICK' => '银联无卡支付',
        'WAPCCARD' => 'MOB信用卡',
        'WAPDCARD' => 'MOB借记卡',
        'WAPGWCARD' => 'MOB平台',
        'WAPQCCARD' => '手机端快捷支付信用卡',
        'WAPQDCARD' => '手机端快捷支付借记卡',
        'WECHATAPP' => '微信APP',
        'WECHATMOB' => '微信MOB',
        'WECHATSTD' => '原生支付',
        'WECHATWEB' => '微信扫码',
    );
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
     * 获取支付相关的接口
     * @param type $params
     * @return boolean
     */
    public function getOrderPay($params = array()) {
        if (empty($params['pay_id'])) {
            return FALSE;
        }
        //单笔支付单查询请求
        $search_params = array(
            'busiTypeId' => 'DOOTA',
            'merchantId' => 'MLS_I_00000001',
            'payId' => $params['pay_id'],
            'version' => '20131111',
        );
        $query = http_build_query($search_params);
        $signature = self::PAY_KEY . $query;
        $search_params['HMAC'] = md5($signature);
        $url_pay = self::MPAY_URL . 'queryPayinfo.do';
        $payInfo = VirusCurlTool::getInstance()->post($url_pay, $search_params);
        $payInfo = json_decode($payInfo,true);
       
        if (!empty($payInfo['data'])) {
            $trxId = $payInfo['data']['trxId'];
            switch ($payInfo['data']['status']) {
                case 'SUCCESS':
                    $payInfo['data']['status'] = '支付成功';
                    break;
                case 'FAILED':
                    $payInfo['data']['status'] = '支付失败';
                    break;
                default:
                    $payInfo['data']['status'] = '状态未知';
                    break;
            }
            $payInfo['data']['pay_mode'] = isset($this->pay_mode_map[$payInfo['data']['pmCode']]) ? $this->pay_mode_map[$payInfo['data']['pmCode']] : $payInfo['data']['pmCode'];
        }
        //从接口时时的去读 ，如果读取不到，取混存数据   
        $cacheKey = 'Joint:OrderPay:getOrderPay:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($payInfo)) {
            $payInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($payInfo)) {
                $payInfo = json_decode($payInfo, true);
                return $payInfo['data'];
            }   
        }else{
            //生成缓存   
            BaseMemcache::instance()->set($cacheKey, json_encode($payInfo), self::$expireTime);
            return $payInfo['data'];
        }
                
        return FALSE;
    }
    
    
    /**
     * 单笔退款单查询请求
     * @param type $params
     * @return boolean
     */
    public function getRefundorder($params = array()) {
        if (empty($params['pay_id'])) {
            return FALSE;
        }
        $search_params = array(
            'busiTypeId' => 'DOOTA',
            'merchantId' => 'MLS_I_00000001',
            'payId' => $params['pay_id'],
            'version' => '20131111',
        );
        $query = http_build_query($search_params);
        $signature = self::PAY_KEY . $query;
        $search_params['HMAC'] = md5($signature);
        $url_refund_pay = self::MPAY_URL . 'queryRefundorder.do';
        $refundPayInfo =  VirusCurlTool::getInstance()->post($url_refund_pay, $search_params);
              
        $refundPayInfo = json_decode($refundPayInfo,true);
        $refundPayData = array();
        if (!empty($refundPayInfo['data']['refundpays'])) {
            foreach ($refundPayInfo['data']['refundpays'] as $key => &$refundpaysitem) {
                switch ($refundpaysitem['status']) {
                    case 'SUCCESS':
                        $refundpaysitem['status'] = '退款成功';
                        break;
                    case 'FAILED':
                        $refundpaysitem['status'] = '退款失败';
                        break;
                    default:
                        $refundpaysitem['status'] = '状态未知';
                        if (!empty($refundpaysitem['errMsg'])) {
                            $refundpaysitem['status'] = $refundpaysitem['errMsg'];
                        }
                        break;
                }
            }
            $refundPayData = $refundPayInfo['data']['refundpays'];
            
        }
        
        $cacheKey = 'Joint:OrderPay:getRefundorder:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if (empty($refundPayData)) {
            $refundPayData = BaseMemcache::instance()->get($cacheKey);
            return json_decode($refundPayData, true);
        }else{
            BaseMemcache::instance()->set($cacheKey, json_encode($refundPayData), self::$expireTime);
            return $refundPayData;
        }
        return FALSE;
    }

    /**
     * @param array $params
     * @return bool|mixed
     */
    public function getPayListByOrderId($params = array()){
        if (empty($params['order_id'])) {
            return FALSE;
        }
        //单笔支付单查询请求
        $search_params = array(
            'busiTypeId' => 'DOOTA',
            'merchantId' => 'MLS_I_00000001',
            'orderNo' => $params['order_id'],
            'version' => '20131111',
        );
        $query = http_build_query($search_params);
        $signature = self::PAY_KEY . $query;
        $search_params['HMAC'] = md5($signature);
        $url_pay = self::MPAY_URL . 'queryPayInfo/list.do';
        $payInfo = VirusCurlTool::getInstance()->post($url_pay, $search_params);
        $payInfo = json_decode($payInfo,true);

        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:OrderPay:getPayListByOrderId:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($payInfo['data'])) {
            $payInfo = BaseMemcache::instance()->get($cacheKey);
            if (!empty($payInfo)) {
                $payInfo = json_decode($payInfo, true);
                return $payInfo['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($payInfo), self::$expireTime);
            return $payInfo['data'];
        }

        return FALSE;
    }

}

?>