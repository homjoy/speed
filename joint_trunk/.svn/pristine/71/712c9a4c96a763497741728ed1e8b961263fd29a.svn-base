<?php
namespace Joint\Package\Order;

use Joint\Package\Common\VirusCurlTool;
use Frame\Speed\Exception\ParameterException;

/**
 * Class RefundService
 * @package Joint\Package\Order
 * @author yongzhao
 * @desc 退款服务
 */
class RefundService extends \Joint\Package\Common\BasePackage {

    const ORDER_URL = 'http://virusdoota.meilishuo.com/';
    //const ORDER_URL = 'http://virusdoota.sichongcheng.rdlab.meilishuo.com/';
    public static $instance = null;
    private static $expireTime = 259200;
    private static $apeal_return_code = array(
        0       => "操作成功",
        29001   => "用户必须登录",
        29002   => "refund_id不能为空",
        29003   => "type不能为空",
        29004   => "必传参数有空值",
        29005   => "退款单不存在",
        29006   => "退款状态不合法",
        20101   => "不正确的退款类型",
        20104   => "拒绝退款缺少拒绝原因",
        20105   => "错误的操作类型"
    );

    private function __construct() {}

    public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function appealArbitrate($params = array()){
        if(empty($params['refund_id']) || empty($params['type'])){
            return FALSE;
        }

        $url = self::ORDER_URL . 'appeal/appeal_arbitrate';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function dealRefund($params = array()){
        if(empty($params['refund_id']) || empty($params['type'])){
            return FALSE;
        }

        $url = self::ORDER_URL . 'refund/refund_work_order';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function refundReason($params = array()){
        if(empty($params['refund_id'])){
            return FALSE;
        }

        $url = self::ORDER_URL . 'refund/refund_reason';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function refundServiceReply(){
        if(empty($params['refund_id']) || empty($params['type'])){
            return FALSE;
        }

        $url = self::ORDER_URL . 'refund/refund_service_reply';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }

    /**
     * @return bool
     * @throws \Frame\Speed\Exception\ParameterException
     */
    public static function refundServiceGoods(){
        if(empty($params['refund_id']) || empty($params['type'])){
            return FALSE;
        }

        $url = self::ORDER_URL . 'refund/refund_service_goods';

        $ret = VirusCurlTool::getInstance()->post($url, $params);

        $ret = json_decode($ret, true);
        if($ret['error_code'] !== 0){
            throw new ParameterException(self::$apeal_return_code[$ret['error_code']],10004);
        }
        return $ret['data'];
    }
}

?>