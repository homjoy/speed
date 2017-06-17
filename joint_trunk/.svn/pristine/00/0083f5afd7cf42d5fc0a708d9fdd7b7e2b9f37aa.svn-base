<?php
namespace Joint\Package\Order;

use Libs\Util\ArrayUtilities;
use Joint\Package\Common\BaseMemcache;
use \Joint\Package\Common\VirusCurlTool;

/**
 * Class OrderWeixin
 * @package Joint\Package\Order
 * @author yongzhao
 * @desc 微信订单相关
 */
class OrderWeixin extends \Joint\Package\Common\BasePackage{

    const VIRUS_DOMAIN = "http://virusdoota.meilishuo.com/";

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

    public function getOrderByTransId($params = array()){
        if(empty($params['trans_id'])){
            return FALSE;
        }

        $orderInfo = VirusCurlTool::getInstance()->post(self::VIRUS_DOMAIN . 'order/order_info_weixin', $params);

        $cacheKey = 'Joint:OrderWeixin:getOrderByTransId:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        if(!empty($orderInfo)){
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $orderInfo, self::$expireTime);
        }else{
            //查询缓存
            $orderInfo = BaseMemcache::instance()->get($cacheKey);
        }
        $orderInfo = json_decode($orderInfo, true);

        $returnData = !empty($orderInfo['data']) ? $orderInfo['data'] : FALSE;
        return $returnData;
    }
}
?>