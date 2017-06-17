<?php

namespace Joint\Package\Shop;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;
use Frame\Speed\Exception\ParameterException;

defined('VIRUS_URL') || define("VIRUS_URL", 'http://virusdoota.meilishuo.com/');
//defined('VIRUS_URL') || define("VIRUS_URL", 'http://virusdoota.sichongcheng.rdlab.meilishuo.com/');

/**
 * 商家仲裁
 * Class ShopArbitrate
 * @package Joint\Package\Shop
 * @author yongzhao
 */
class ShopArbitrate extends \Joint\Package\Common\BasePackage {

    const ORDER_URL = 'http://virusdoota.meilishuo.com/';
    //const ORDER_URL = 'http://virusdoota.sichongcheng.rdlab.meilishuo.com/';
    private static $instance = null;
    private static $expireTime = 259200;

    private static $apeal_return_code = array(
        140001  => "参数验证错误",
        290001  => "退款信息不存在",
        290002  => "退款状态必须是发货前",
        290003  => "更新数据失败",
        290004  => "更新数据失败",
    );

    private function __construct() {}

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 退款原因仲裁工单处理
     * @param array $params
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public function updateRefundReason($params = array()){
        if(empty($params['refund_id']) ||
            empty($params['appeal_id']) ||
            empty($params['refund_reason']) ||
            empty($params['refund_reason_id']) ||
            empty($params['appeal_type'])){
            return FALSE;
        }

        $url = VIRUS_URL . 'appeal/appeal_shop_arbitrate';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * 风控系统/工单系统更新退款原因
     * @param array $params
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public function updateRefundRiskReason($params = array()){
        if(empty($params['refund_id']) ||
            empty($params['select_reason']) ||
            empty($params['select_reason_id']) ||
            empty($params['service_type'])){
            return FALSE;
        }

        $url = VIRUS_URL . 'refund/refund_risk_reason';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * 商家仲裁详情
     * @param array $params
     * @return bool
     */
    public function getAppealShopDetail($params = array()){
        if(empty($params['user_id']) ||
            empty($params['shop_id']) ||
            empty($params['refund_id'])){
            return FALSE;
        }
        $url = VIRUS_URL . 'appeal/appeal_shop_detail';
        $result = VirusCurlTool::getInstance()->post($url,$params);
        $result = json_decode($result, true);

        //从接口时时的去读 ，如果读取不到，取混存数据
        $cacheKey = 'Joint:ShopArbitrate:getAppealShopDetail:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        //查询缓存
        if(empty($result)) {
            $result = BaseMemcache::instance()->get($cacheKey);
            if (!empty($result)) {
                $result = json_decode($result, true);
                return $result['data'];
            }
        }else{
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, json_encode($result), self::$expireTime);
            return $result['data'];
        }
        return FALSE;
    }
}
