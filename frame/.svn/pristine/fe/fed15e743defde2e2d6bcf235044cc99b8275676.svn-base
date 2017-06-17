<?php
namespace Libs\Mark;

class MemPosterMark extends \Libs\Cache\NormalCacheBase {
    //打标商品cache 

    const PREFIX = 'Mark:Poster:';
    //超时时间(s)
    const TIMEOUT = 600;

    //静态memcache
    static $instance;

    static public function instance() {
        if(!isset(static::$instance)) {
            $prefix = static::PREFIX . date('H');
            static::$instance = new self($prefix);
            static::$instance->setTime(static::TIMEOUT);
        }
        return static::$instance;
    }
    
}

class ShopBigPromote {

    const ACT_TYPE_MEIZHUANG = 'meizhuang';
    const ACT_TYPE_GROUPON = 'groupon';
    const ACT_TYPE_PROMOTE = 'promote';
    const ACT_TYPE_NORMAL = 'normal';

    private static $goods_info_table = 'campaign_goods_info';

    const ACT_ID_520_CATEGORY = 33;
    const ACT_ID_520_LOVERS = 35;
    const ACT_ID_618_CATEGORY = 65;
    const ACT_ID_820_CATEGORY = 289; 
    const ACT_ID_820_MEIZHUANG = 321;
    const ACT_ID_916_CATEGORY = 411;
    const ACT_ID_1111_CATEGORY = 541;
    
    
    private static $CurActId = 0;
    private static $curAttrId = 0;

    public static function GetEventGoodsInfosFromApi($aid, $gids) {
        $aid = intval($aid);
        if (empty($aid) || empty($gids) || !is_array($gids)) {
            return array();
        }
        
        $paramData['type'] = 'operate';
        $paramData['aid'] = $aid;
        $paramData['goods_id'] = implode(',', $gids);;
        $paramData['audit_status'] = 4;

        $param = array('url' => 'shopevent/select_event_goods_new');
        $opt = array('timeout' => 3, 'connect_timeout' => 3);
        \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 10, 'ip' => '127.0.0.2'));
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('virus', $param['url'], $paramData, $opt);

        $goodsInfos = array();
        if ($result['httpcode'] == 200 && isset($result['content']) && $result['content']['error_code'] == 0) {
            $goodsInfos = $result['content']['data'];
        } else {
//             $logObj = new \Snake\Libs\Base\SnakeLog('EventGoodsInfosFromApi', 'normal');
//             $logObj->w_log($aid . ':' . print_r($paramData['goods_id'], TRUE) . ':' . print_r($result, TRUE));
        }

        if (empty($goodsInfos)) {
            return array();
        }

        $resultData = array();
        foreach ($goodsInfos as $item) {
            $goodsId = $item['goods_id'];
            $resultData[$goodsId] = array(
                'aid' => $item['aid'],
                'goods_id' => $item['goods_id'],
                'twitter_id' => $item['twitter_id'],
                'goods_price' => $item['goods_price'],
                'campaign_price' => $item['campaign_price'],
                'campaign_off' => $item['campaign_off'],
                //'current_price' => $item['current_price'],
                'phone_price' => $item['phone_price'],
            );
        }

        return $resultData;
    }
    
    private static function requestDootaGoods($gids = array(), $field = '') {
        if (!is_array($gids) || empty($gids)) {
            return array();
        }

        $paramData = array();
        $tmpGids = array();
        foreach ($gids as $gid) {
            if (is_numeric($gid) && $gid > 0) {
                $tmpGids[] = $gid;
            }
        }

        if (empty($tmpGids)) {
            return array();
        }

        $paramData['goods_id'] = implode(',', $tmpGids);
        if (!empty($field)) {
            $paramData['fields'] = $field;
        }

        $param = array('url' => 'cargo/cargo_info');
        \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 0, 'ip' => '127.0.0.2'));
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('virus', $param['url'], $paramData);
        if ($result['httpcode'] == 200 && isset($result['content']) && $result['content']['error_code'] == 0) {
            $dootaGoods = $result['content']['data'];
            return $dootaGoods['cargo'];
        } else {
            //     			$logObj = new \Snake\Libs\Base\SnakeLog('doota_cargo_info_api', 'normal');
            //     			$logObj->w_log($paramData['goods_id'] . ':' . print_r($result, TRUE));
        }

        return array();
    }

    public static function GetEventGoodsInfosFromApi_710($gids) {
        if (empty($gids) || !is_array($gids)) {
            return array();
        }

        $field = 'goods_id,twitter_id,goods_price,origin_price,goods_title,is_promote,goods_on_shelf';
        //         $dootaObj = new \Snake\Package\Doota\DootaCargo($gids);
        //         $goodsInfos = $dootaObj->getDootaCargoGoods($field,TRUE);
        $goodsInfos = self::requestDootaGoods($gids, $field);

        if (empty($goodsInfos)) {
            return array();
        }

        $resultData = array();
        foreach ($goodsInfos as $item) {
            $goodsId = $item['goods_id'];
            $off = 0;
            if (!empty($item['origin_price'])) {
                $off = number_format($item['goods_price']/$item['origin_price'] * 10, 1);
            }
            $resultData[$goodsId] = array(
                    'goods_id' => $item['goods_id'],
                    'twitter_id' => $item['twitter_id'],
                    'goods_price' => $item['origin_price'],
                    'campaign_price' => $item['goods_price'],
                    'current_price' => $item['goods_price'],
                    'goods_title' => $item['goods_title'],
                    'goods_on_shelf' => $item['goods_on_shelf'],
                    'campaign_off' => $off,
                    'price_mark' => intval(floatval($item['goods_price']) * 10) * 10,
                    'discount_mark' => intval(floatval($off) * 10) ,
                    );
        }

        return $resultData;
    }

    public static function GetEventGrouponInfosFromApi($event_id, $goods_ids) {
        $event_id = intval($event_id);
        if (empty($event_id) || empty($goods_ids) || !is_array($goods_ids)) {
            return array();
        }

        $paramData = array();
        $paramData['goods_ids'] = implode(',', $goods_ids);
        $paramData['event_id'] = $event_id;

        $param = array('url' => 'groupon/groupon_off_price_for_event');
        $opt = array('timeout' => 3, 'connect_timeout' => 3);
        \Libs\Serviceclient\SnakeHeaderCreator::setInfos(array('user_id' => 10, 'ip' => '127.0.0.2'));
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('virus', $param['url'], $paramData, $opt);

        $goodsInfos = array();
        if ($result['httpcode'] == 200 && isset($result['content']) && $result['content']['error_code'] == 0) {
            $goodsInfos = $result['content']['data'];
        } else {
            //             $logObj = new \Snake\Libs\Base\SnakeLog('GetEventGrouponInfosFromApi', 'normal');
            //             $logObj->w_log($goods_ids . ':' . print_r($paramData['goods_id'], TRUE) . ':' . print_r($result, TRUE));
        }

        if (empty($goodsInfos)) {
            return array();
        }

        $resultData = array();
        foreach ($goodsInfos as $item) {
            $goodsId = $item['goods_id'];
            $resultData[$goodsId] = array(
                    'event_id' => $event_id,
                    'goods_id' => $item['goods_id'],
                    'twitter_id' => $item['twitter_id'],
                    'campaign_price' => $item['campaign_price'],
                    'campaign_off' => $item['campaign_off'],
                    );
        }

        return $resultData;
    }

    public static function GetEventMeizhuangInfosFromApi($event_id, $goods_ids) {
        $event_id = intval($event_id);
        if (empty($event_id) || empty($goods_ids) || !is_array($goods_ids)) {
            return array();
        }

        $paramData = array();
        $paramData['goods_ids'] = implode(',', $goods_ids);
        $paramData['event_id'] = $event_id;

        $param = array('url' => 'promote/activity_goods_mark');
        $opt = array('timeout' => 3, 'connect_timeout' => 3);
        \Libs\Serviceclient\SnakeHeaderCreator::setInfos(array('user_id' => 10, 'ip' => '127.0.0.2'));
        $clientObj = new \Libs\Serviceclient\Client();
        $result = $clientObj->call('virus', $param['url'], $paramData, $opt);

        $goodsInfos = array();
        if ($result['httpcode'] == 200 && isset($result['content']) && $result['content']['error_code'] == 0) {
            $goodsInfos = $result['content']['data'];
        } else {
            //             $logObj = new \Snake\Libs\Base\SnakeLog('GetEventMeizhuangInfosFromApi', 'normal');
            //             $logObj->w_log($goods_ids . ':' . print_r($paramData['goods_id'], TRUE) . ':' . print_r($result, TRUE));
        }

        if (empty($goodsInfos)) {
            return array();
        }

        $resultData = array();
        foreach ($goodsInfos as $item) {
            $goodsId = $item['goods_id'];
            $resultData[$goodsId] = array(
                    'event_id' => $event_id,
                    'goods_id' => $item['goods_id'],
                    'twitter_id' => $item['twitter_id'],
                    'campaign_price' => $item['campaign_price'],
                    'campaign_off' => $item['campaign_off'],
                    );
        }

        return $resultData;
    }

    public static function getPromoteMarkInfo($gids, $type, $aid = 0) {
        $aid = intval($aid);
        if (empty($gids) || !is_array($gids)) {
            return array();
        }

        $cacheHitsData = MemPosterMark::instance()->getCache($gids);
        $noHitsGids = MemPosterMark::instance()->notInCache();
        !is_array($cacheHitsData) && $cacheHitsData = array();
        $infos = array();

        do {
            if (empty($noHitsGids)) {break;}
            $goods_ids = array();
            foreach ($noHitsGids as $noHitsGid) {
                $noHitsGid = intval($noHitsGid);
                if ($noHitsGid > 0) {
                    $goods_ids[] = $noHitsGid;
                }
            }
            if (empty($goods_ids)) {break;}

            if ($type == self::ACT_TYPE_PROMOTE) {
                $goodsInfos = self::GetEventGoodsInfosFromApi($aid, $goods_ids);
                !is_array($goodsInfos) && $goodsInfos = array();
                foreach ($goodsInfos as $ginfo) {
                    $sale_mark = intval(floatval($ginfo['campaign_off']) * 10);
                    $price_mark = ceil(floatval($ginfo['campaign_price']) * 10) * 10;
                    $gid = $ginfo['goods_id'];
                    $infos[$gid] = array(
                            'goods_id' => $gid,
                            'twitter_id' => $ginfo['twitter_id'],
                            'price_mark' => $price_mark,
                            'sale_mark' => $sale_mark,
                            'off_mark' => 100 - $sale_mark,
                            );
                }
            } elseif ($type == self::ACT_TYPE_GROUPON) {
                $goodsInfos = self::GetEventGrouponInfosFromApi($aid, $goods_ids);
                !is_array($goodsInfos) && $goodsInfos = array();
                foreach ($goodsInfos as $ginfo) {
                    $sale_mark = intval(floatval($ginfo['campaign_off']) * 10);
                    $price_mark = ceil(floatval($ginfo['campaign_price']) * 10) * 10;
                    $gid = $ginfo['goods_id'];
                    $infos[$gid] = array(
                            'goods_id' => $gid,
                            'twitter_id' => $ginfo['twitter_id'],
                            'price_mark' => $price_mark,
                            'sale_mark' => $sale_mark,
                            'off_mark' => 100 - $sale_mark,
                            );
                }
            } elseif ($type == self::ACT_TYPE_MEIZHUANG) {
                $goodsInfos = self::GetEventMeizhuangInfosFromApi($aid, $goods_ids);
                !is_array($goodsInfos) && $goodsInfos = array();
                foreach ($goodsInfos as $ginfo) {
                    $sale_mark = intval(floatval($ginfo['campaign_off']) * 10);
                    $price_mark = ceil(floatval($ginfo['campaign_price']) * 10) * 10;
                    $gid = $ginfo['goods_id'];
                    $infos[$gid] = array(
                            'goods_id' => $gid,
                            'twitter_id' => $ginfo['twitter_id'],
                            'price_mark' => $price_mark,
                            'sale_mark' => $sale_mark,
                            'off_mark' => 100 - $sale_mark,
                            );
                }
            } elseif ($type == self::ACT_TYPE_NORMAL) {
                $goodsInfos = self::GetEventGoodsInfosFromApi_710($goods_ids);
                !is_array($goodsInfos) && $goodsInfos = array();
                foreach ($goodsInfos as $ginfo) {
                    $sale_mark = 0;
                    if (!empty($ginfo['goods_price'])) {
                        $sale_mark = intval($ginfo['current_price']/$ginfo['goods_price'] * 100);
                    }
                    $price_mark = ceil(floatval($ginfo['current_price']) * 10) * 10;
                    $gid = $ginfo['goods_id'];
                    $infos[$gid] = array(
                            'goods_id' => $gid,
                            'twitter_id' => $ginfo['twitter_id'],
                            'price_mark' => $price_mark,
                            'sale_mark' => $sale_mark,
                            'off_mark' => 100 - $sale_mark,
                            );
                }
            }

            if (empty($infos)) {break;}

            MemPosterMark::instance()->setCache($infos);
        } while(FALSE);

        return $cacheHitsData + $infos;
    }

}
