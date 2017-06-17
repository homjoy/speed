<?php
namespace Libs\Coupon;

use \Libs\Util\Utilities;

class CouponApplyUtilities {
    public static function searchCouponApply($searchType, $params, $offset = 0, $limit = 10) {
        $searchParams = array(
            'search_type' => $searchType,
            'params' => $params,
            'offset' => $offset,
            'limit' => $limit,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $searchResult = $clientObj->call('coupon', 'coupon/search_coupon_apply', $searchParams);

        if ($searchResult['httpcode'] == 200 && $searchResult['content']['error_code'] == 0) {
            $searchResult = $searchResult['content']['data'];
        } else {
            $searchResult = array(
                'code' => -1,
                'message' => '系统错误'
            );
        }
        return $searchResult;
    }

    public static function getShopCouponApply($shop_id, $user_id, $service = 'virus') {
        $params = array(
            'shop_id' => $shop_id,
            'user_id' => $user_id,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('coupon', 'coupon/get_shop_coupon_apply', $params);

        $shopCouponApply = array();
        if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
            $shopCouponApply = $result['content']['data'];
        }
        return $shopCouponApply;
    }


    public static function batchGetShopCouponApply($shopIds, $user_id, $service = 'virus') {
        $params = array(
            'shop_ids' => $shopIds,
            'user_id' => $user_id,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('coupon', 'coupon/batch_get_shop_coupon_apply', $params);

        $shopCouponApply = array();
        if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
            $shopCouponApply = $result['content']['data'];
        }
        return $shopCouponApply;
    }

    public static function batchGetShopCouponApplyDetail($shop_id, $user_id, $service = 'virus') {
        $shopCouponApply = self::batchGetShopCouponApply($shop_id, $user_id, $service);
        $shopCouponApply = self::filterShopCouponApply($shopCouponApply);
        $shopCouponApply = self::formatShopCouponApply($shopCouponApply);
        return $shopCouponApply;
    }

    public static function getShopCouponApplyDetail($shop_id, $user_id, $service = 'virus') {
        $shopCouponApply = self::getShopCouponApply($shop_id, $user_id, $service);
        $shopCouponApply = self::filterShopCouponApply($shopCouponApply);
        $shopCouponApply = self::formatShopCouponApply($shopCouponApply);
        return $shopCouponApply;
    }

    public static function filterShopCouponApply($shopCouponApply) {
        $filteredShopCouponApply = array();
        foreach ($shopCouponApply as $shopCouponApplyRecord) {
            if ($shopCouponApplyRecord['show_filters'] & CouponConstantPool::COUPON_APPLY_SHOW_FILTER_SHOP_HOME != 0) {
                continue;
            }
            $filteredShopCouponApply[] = $shopCouponApplyRecord;
        }
        return $filteredShopCouponApply;
    }

    public static function formatShopCouponApply($shopCouponApply) {
        $formattedShopCouponApply = array();
        foreach ($shopCouponApply as $shopCouponApplyRecord) {
            $formattedShopCouponApplyRecord = array(
                'coupon_apply_code' => $shopCouponApplyRecord['coupon_apply_code'],
                'title' => $shopCouponApplyRecord['title'],
                'threshold' => $shopCouponApplyRecord['threshold'],
                'credit' => $shopCouponApplyRecord['credit'],
                'can_apply_status' => $shopCouponApplyRecord['can_apply_status'],
            );
            if (!empty($shopCouponApplyRecord['coupon_valid_time_range'])) {
                $formattedShopCouponApplyRecord['time_tips'] = "有效期：" . date('n.j', strtotime($shopCouponApplyRecord['coupon_valid_time_range']['start_time'])) . "-" . date('n.j', strtotime($shopCouponApplyRecord['coupon_valid_time_range']['expire_time']) - 1);
            }
            $formattedShopCouponApply[] = $formattedShopCouponApplyRecord;
        }
        return $formattedShopCouponApply;
    }

    public static function queryCouponApply($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        $queryParams = array(
            'params' => $params,
            'cols' => $cols,
            'from_master' => !empty($fromMaster) ? 1 : 0,
            'hash_key' => $hashKey,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('coupon', 'coupon/query_coupon_apply', $queryParams, array('timeout' => 1));

        if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
            $couponApply = $result['content']['data'];
        } else {
            $couponApply = array();
        }
        return $couponApply;
    }

    public static function getCouponApply($couponApplyCode) {
        $params = array(
            'coupon_apply_code' => $couponApplyCode,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('coupon', 'coupon/get_coupon_apply', $params);

        $couponApply = NULL;
        if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
            $couponApply = $result['content']['data'];
        }
        return $couponApply;
    }

    public static function judgeCanApplyStatus($user_id, $couponApplyCodes) {
        $params = array(
            'user_id' => $user_id,
            'coupon_apply_codes' => $couponApplyCodes,
        );
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('coupon', 'coupon/judge_can_apply_status', $params, array('timeout' => 1));

        $judgeResult = array();
        if ($result['httpcode'] == 200 && $result['content']['error_code'] == 0) {
            $judgeResult = $result['content']['data'];
        }
        return $judgeResult;
    }

    public static function canApplyStatus($user_id, $couponApplyCode) {
        $orignalCouponApplyCode = $couponApplyCode;
        $couponApplyCodes = !is_array($couponApplyCode) ? array($couponApplyCode) : $couponApplyCode;
        $judgeResult = self::judgeCanApplyStatus($user_id, $couponApplyCodes);
        if (!is_array($orignalCouponApplyCode)) {
            if (isset($judgeResult[$orignalCouponApplyCode])) {
                return $judgeResult[$orignalCouponApplyCode];
            } else {
                return CouponConstantPool::CAN_APPLY_UNKNOWN_STATUS;
            }
        } else {
            $canApplyStatus = $judgeResult;
            foreach ($couponApplyCodes as $couponApplyCode) {
                if (!isset($canApplyStatus[$couponApplyCode])) {
                    $canApplyStatus[$couponApplyCode] = CouponConstantPool::CAN_APPLY_UNKNOWN_STATUS;
                }
            }
            return $canApplyStatus;
        }
    }

    public static function attachCanApplyStatus($couponApply, $user_id) {
        if (empty($user_id)) {
            foreach ($couponApply as $key => $couponApplyRecord) {
                $couponApply[$key]['can_apply_status'] = CouponConstantPool::CAN_APPLY_USER_LOGOUT_STATUS;
            }
        } else {
            $couponApplyCodes = Utilities::DataToArray($couponApply, 'coupon_apply_code');
            $judgeResult = self::judgeCanApplyStatus($user_id, $couponApplyCodes);
            foreach ($couponApply as $key => $couponApplyRecord) {
                $couponApplyCode = $couponApplyRecord['coupon_apply_code'];
                $couponApply[$key]['can_apply_status'] = $judgeResult[$couponApplyCode];
            }
        }
        return $couponApply;
    }

    public static function extractBasicCouponMeta($couponApply) {
        $basicCouponMetas = array();
        foreach ($couponApply as $couponApplyRecord) {
            $basicCouponMetas[] = array(
                'coupon_type' => $couponApplyRecord['coupon_type'],
                'coupon_meta_id' => $couponApplyRecord['coupon_meta_id'],
            );
        }
        return $basicCouponMetas;
    }

    public static function extractCouponApplyAbstract($couponApply, $couponMeta) {
        $couponApplyAbstract = array(
            'coupon_apply_code' => $couponApply['coupon_apply_code'],
            'coupon_type' => $couponApply['coupon_type'],
            'coupon_meta_id' => $couponApply['coupon_meta_id'],
            'threshold' => $couponMeta['threshold'],
            'credit' => $couponMeta['credit'],
            'begin_time' => $couponApply['begin_time'],
            'end_time' => $couponApply['end_time'],
            'ctime' => $couponApply['ctime'],
            'shop_id' => isset($couponMeta['shop_id']) ? $couponMeta['shop_id'] : 0,
        );
        isset($couponApply['can_apply_status']) && $couponApplyAbstract['can_apply_status'] = $couponApply['can_apply_status'];
        return $couponApplyAbstract;
    }

    public static function filterCouponApply($couponApply, $filterMask, $preserve = FALSE) {
        $filteredCouponApply = array();
        if(!is_array($couponApply)) {
            //$log = new \Libs\Base\SnakeLog("coupon_filter_error_usage","normal");
            //$log->w_log($_SERVER['REQUEST_URI']);
            $couponApply = array();
        }
        foreach ($couponApply as $key => $couponApplyRecord) {
            if ($couponApplyRecord['show_filters'] & $filterMask != 0) {
                continue;
            }
            if ($preserve) {
                $filteredCouponApply[$key] = $couponApplyRecord;
            } else {
                $filteredCouponApply[] = $couponApplyRecord;
            }
        }
        return $filteredCouponApply;
    }
}
