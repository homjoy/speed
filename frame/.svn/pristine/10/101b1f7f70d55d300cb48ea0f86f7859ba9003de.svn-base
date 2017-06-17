<?php
namespace Libs\Mark;

use \Libs\Mark\Utilities;
use \Libs\Mark\ExpressCommonTool;
use \Libs\Mark\FunctionLimit;
use \Libs\Mark\ShopBigPromote;

class FashionMarks {
    private static $position = array(
        'upleft'        => 1,
        'up'            => 2,
        'upright'       => 3,
        'left'          => 4,
        'center'        => 5,
        'right'         => 6,
        'downleft'      => 7,
        'down'          => 8,
        'downright'     => 9,
    );

    private static $direction = array(
        'horizontal'    => 1,
        'tilt'          => 2,
        'vertical'      => 3,
    );

    private static $platforms = array(
        'Web'       => 1,
        'Wap'       => 2,
        'iPhone'    => 3,
        'iPad'      => 4,
        'Android'   => 5,
        'WindowsPhone' => 6,
    );

    private static $types = array(
        'poster'   => 0, // 全站
        'groupon'   => 2, // 团购
        'discount'  => 3, // 特卖
        'weixin' => 4, // 微信商城
        'single' => 5, // 单品页
        'mz_single' => 6, // 美妆特卖单品
        'shouq' => 7, // 手Q商城
        'activity' => 8, // 活动
    );

    private static $attr_types = array(
        'shopid'   => 1, // 店铺id
    );


    // 获得斜标标信息
    public static function getTiltMarks($gids, $platform, $type) {
        empty($platform) && $platform = 'Web';
        $goods_marks = self::getGoodsMark($gids);
        $mark_ids = Utilities::DataToArray($goods_marks, 'mark_id');
        $mark_ids = array_unique($mark_ids);
        $mark_info = self::getMarkInfo($mark_ids, self::$types[$type]);
        $ids = Utilities::DataToArray($mark_info, 'id');
        $mark_detail = self::getMarkDetail($ids, self::$platforms[$platform], self::$position['upright'], self::$direction['tilt']);

        $show_marks = array();
        foreach ($goods_marks as $goods_mark) {
            $goods_id = $goods_mark['goods_id'];
            $mark_id = $goods_mark['mark_id'];
            if (!empty($mark_detail[$mark_id])) {
                $show_marks[$goods_id][] = array_merge($mark_info[$mark_id], $mark_detail[$mark_id]);
            }
        }
        foreach ($show_marks as $goods_id => $show_mark) {
            $show_marks[$goods_id] = Utilities::sortArray($show_mark, 'order', 'ASC');
        }

        return $show_marks;
    }

    public static function getUpLeftMarks($mark_ids, $platform = '', $type = 'discount') {
        if (empty($mark_ids) || !is_array($mark_ids)) {
            return array();
        }
        empty($platform) && $platform = 'Web';

        $mark_info = self::getMarkInfo($mark_ids, self::$types[$type]);
        if (empty($mark_info)) {
            return array();
        }

        $ids = Utilities::DataToArray($mark_info, 'id');
        $mark_detail = self::getMarkDetail($ids, self::$platforms[$platform], self::$position['upleft'], self::$direction['horizontal']);
        $marks = array();
        foreach ($mark_detail as $mark_id => $single_mark) {
            $single_mark['img_url'] = Utilities::convertPicture($single_mark['img_url']);
            $marks[] = array_merge($mark_info[$mark_id], $single_mark);
        }
        $marks = Utilities::sortArray($marks, 'order', 'ASC');
        return $marks;
    }

    private static function getGoodsMark($gids) {
        if (empty($gids)) {
            return array();
        }
        $params = array(
            'goods_id' => $gids,
            'status' => 0,
        );
        return FashionGoodsMarks::getGoodsMark($params, 'goods_id, mark_id');
    }

    private static function getAttrMark($data_ids, $attr_type) {
        if (empty($data_ids) || empty($attr_type)) {
            return array();
        }
        $params = array(
            'data_id' => $data_ids,
            'type' => $attr_type,
            'status' => 0,
        );
        return FashionGoodsMarks::getMark($params, 'data_id, type, mark_id');
    }

    private static function getMarkDetail($mark_ids, $platform, $position, $direction) {
        empty($platform) && $platform = 1;
        if (empty($mark_ids)) {
            return array();
        }
        $params = array(
            'mark_id' => $mark_ids,
            'platform' => $platform,
            'position' => $position,
            'direction' => $direction,
            'status' => 0,
        );
        return FashionGoodsMarks::getMarkDetail($params, 'mark_id, img_url, img_width, img_height, mark_text', FALSE, 'mark_id');
    }

    private static function getMarkInfo($mark_ids, $type) {
        if (empty($mark_ids)) {
            return array();
        }
        $params = array(
            'id' => $mark_ids,
            'type' => $type,
            'status' => 0,
        );
        return FashionGoodsMarks::getMarkInfo($params, '`id`, `order`', FALSE, 'id');
    }

    // 获得横标信息 -- pc/mob/shouq/weixin/haitao --
    public static function getUpleftHorizontalMarks($cargogoods, $platform, $type, $count = 2) {
        $cargogoods = Utilities::changeDataKeys($cargogoods, "goods_id");
        if (empty($cargogoods) || !is_array($cargogoods) || empty($type)) {
            return array();
        }

        empty($platform) && $platform = 'Web';
        $gids = Utilities::DataToArray($cargogoods, "goods_id");        
        $tids = Utilities::DataToArray($cargogoods, "twitter_id");        
        $shopids = Utilities::DataToArray($cargogoods, "shop_id");        

        $goods_marks = self::getGoodsMark($gids);
        $shop_marks = self::getAttrMark($shopids, self::$attr_types['shopid']);
        $goods_mark_ids = Utilities::DataToArray($goods_marks, 'mark_id');
        $shop_mark_ids = Utilities::DataToArray($shop_marks, 'mark_id');
        $mark_ids = array_merge($goods_mark_ids, $shop_mark_ids);
        $mark_ids = array_unique($mark_ids);

        $mark_info = self::getMarkInfo($mark_ids, self::$types[$type]);
        $ids = Utilities::DataToArray($mark_info, 'id');
        $mark_details = self::getMarkDetail($ids, self::$platforms[$platform], 
                                self::$position['upleft'], self::$direction['horizontal']);

        $marks = array();
        foreach ($mark_details as $mark_detail) {
            $mark_id = $mark_detail['mark_id'];
            $mark = array();
            $mark['mark_id'] = $mark_id;
            $mark['order'] = $mark_info[$mark_id]['order'];
            $mark['img_url'] = Utilities::convertPicture($mark_detail['img_url']);
            $mark['img_width'] = $mark_detail['img_width'];
            $mark['img_height'] = $mark_detail['img_height'];
            $mark_text = trim($mark_detail['mark_text']);
            !empty($mark_text) && $mark['mark_text'] = $mark_text;
            $marks[$mark_id] = $mark;
        }

        $show_goods_marks = array();
        foreach ($goods_marks as $goods_mark) {
            $goods_id = $goods_mark['goods_id'];
            $mark_id = $goods_mark['mark_id'];
            !empty($marks[$mark_id]) && $show_goods_marks[$goods_id][$mark_id] = $marks[$mark_id];
        }

        $show_shop_marks = array();
        foreach ($shop_marks as $shop_mark) {
            $shop_id = intval($shop_mark['data_id']);
            $mark_id = $shop_mark['mark_id'];
            !empty($marks[$mark_id]) && $show_shop_marks[$shop_id][$mark_id] = $marks[$mark_id];
        }

        $hot_mark_web = array(
            'mark_id' => '89',
            'img_url' => Utilities::convertPicture('img/_o/50/e3/7d6e6c99dd87ed6f09a97c74d14e_37_24.c6.png'),
            'img_width' => '37',
            'img_height' => '24');
        $hot_mark_mob = array(
            'mark_id' => '89',
            'img_url' => Utilities::convertPicture('img/_o/f7/8d/be707422599980f959a651bffe4c_60_34.c6.png'),
            'img_width' => '60',
            'img_height' => '34');
        $hot_mark = ($platform == 'Web') ? $hot_mark_web : $hot_mark_mob;
        $show_marks = array();
        foreach ($cargogoods as $goods) {
            $marks = array();
            $goods_id = $goods['goods_id'];
            $shop_id = intval($goods['shop_id']);
            $is_hot = $goods['is_hot'];

            $_goods_marks = array();
            isset($show_goods_marks[$goods_id]) && $_goods_marks = array_values($show_goods_marks[$goods_id]);
            !is_array($_goods_marks) && $_goods_marks = array();
            $_goods_marks = Utilities::sortArray($_goods_marks, 'order', 'ASC');
              
            $markids = Utilities::DataToArray($_goods_marks, "mark_id");        
            $is_hot && !in_array(89, $markids) && $_goods_marks[] = $hot_mark;
            $marks = array_reverse( array_slice($_goods_marks, 0, $count) );
            
            $_shop_marks = array();
            isset($show_shop_marks[$shop_id]) && $_shop_marks = array_values($show_shop_marks[$shop_id]);
            !is_array($_shop_marks) && $_shop_marks = array();
            $shop_mark_count = count($_shop_marks);
            if ($shop_mark_count >= 2) {
                $marks = array_slice($_shop_marks, 0, 2);
            } else if ($shop_mark_count == 1) {
                $markids = Utilities::DataToArray($marks, "mark_id");        
                $shop_mark_id = $_shop_marks[0]['mark_id'];
                if (!in_array($shop_mark_id, $markids)) {
                    count($marks) == $count && array_shift($marks);
                    array_unshift($marks, $_shop_marks[0]);
                }
            }
            
            // 异形标
            if (count($marks) == 2) {
                if ($marks[0]['img_height'] > $marks[1]['img_height']) {
                    $show_marks[$goods_id] = array($marks[0]);
                    continue;
                } 
                if ($marks[1]['img_height'] > $marks[0]['img_height']) {
                    $show_marks[$goods_id] = array($marks[1]);
                    continue;
                }
            }
            
            // 最大的标
            $all_marks = array_merge($_goods_marks, $_shop_marks);
            $all_marks = Utilities::changeDataKeys($all_marks, 'img_height');
            !is_array($all_marks) && $all_marks = array();
            krsort($all_marks, SORT_NUMERIC);
            $biggest = current($all_marks);
            if ($biggest['img_height'] > $marks[0]['img_height']) {
                $show_marks[$goods_id] = array($biggest);
                continue;
            }
            
            $show_marks[$goods_id] = $marks;
        }
        
        if (!FunctionLimit::OpenPriceOrDiscountMarks()) {
            return $show_marks;
        }
        
        do{
            //break;

            $mark_type_key = '';
            if ($type == 'poster' || $type == 'groupon') {
                if (in_array($platform, array('Web', 'Wap'))) {
                    $mark_type_key = 'poster_pc_mark_type';
                } elseif (in_array($platform, array('iPhone','Android'))) {
                    $mark_type_key = 'poster_mob_mark_type';
                }
            } elseif ($type == 'single' || $type == 'mz_single') {
                if (in_array($platform, array('Web', 'Wap'))) {
                    $mark_type_key = 'single_pc_mark_type';
                } elseif (in_array($platform, array('iPhone','Android'))) {
                    $mark_type_key = 'single_mob_mark_type';
                }
            }
            if (empty($mark_type_key)) {break;}

            $datas = ExpressCommonTool::getExpressInfoByPassword('activity_mark');
            $activity_marks = json_decode($datas[0]['data_json'], TRUE);
            if (!is_array($activity_marks) || empty($activity_marks)) {break;}
            $activity_marks = Utilities::changeDataKeys($activity_marks, 'mark_id');
            $isLab = FunctionLimit::IsPmLab();
            foreach ($activity_marks as $mark_id => $activity_mark) {
                $start_time = strtotime($activity_mark['start_time']);
                $end_time   = strtotime($activity_mark['end_time']);
                $isLab && $start_time = $start_time - 4*24*60*60; // 预览
                $curtime    = time();
                if ($curtime > $end_time || $curtime < $start_time) {
                    unset($activity_marks[$mark_id]);
                }
                if (empty($activity_mark[$mark_type_key])) {
                    unset($activity_marks[$mark_id]);
                }
            }
            if (!is_array($activity_marks) || empty($activity_marks)) {break;}
            
            $mark_info = self::getMarkInfo($mark_ids, self::$types['activity']);
            $activity_mark_ids = Utilities::DataToArray($mark_info, 'id');

            $activity_mark_gids_map = array();
            // 全站标签 优先级最低
            isset($activity_marks['0']) && $activity_mark_gids_map['0'] = $gids;
            foreach ($goods_marks as $goods_mark) {
                $goods_id = $goods_mark['goods_id'];
                $mark_id = $goods_mark['mark_id'];
                if (isset($activity_marks[$mark_id]) && in_array($mark_id, $activity_mark_ids)) {
                    $activity_mark_gids_map[$mark_id][] = $goods_id;
                }
            }

            foreach ($activity_mark_gids_map as $mark_id => $gids) {
                $activity_mark = $activity_marks[$mark_id];
                $promote_activity_id = intval($activity_mark['promote_activity_id']);
                $mark_values = array();

                if ($activity_mark['goods_type'] == 'normal') {
                    $mark_values = ShopBigPromote::getPromoteMarkInfo($gids, ShopBigPromote::ACT_TYPE_NORMAL);
                }

                if ($activity_mark['goods_type'] == 'promote') {
                    $mark_values = ShopBigPromote::getPromoteMarkInfo($gids, ShopBigPromote::ACT_TYPE_PROMOTE, $promote_activity_id);
                }

                if ($activity_mark['goods_type'] == 'meizhuang') {
                    $mark_values = ShopBigPromote::getPromoteMarkInfo($gids, ShopBigPromote::ACT_TYPE_MEIZHUANG, $promote_activity_id);
                }

                if ($activity_mark['goods_type'] == 'groupon') {
                    $mark_values = ShopBigPromote::getPromoteMarkInfo($gids, ShopBigPromote::ACT_TYPE_GROUPON, $promote_activity_id);
                }

                if (empty($mark_values) || !is_array($mark_values)) {continue;}
                if (!empty($activity_mark['value_region'])) {
                    $regions = explode('-', $activity_mark['value_region']);
                    $start = intval($regions[1]); $end = intval($regions[2]);
                    $value_type_key = $regions[0] . '_mark';
                    $in_array = in_array($regions[0], array('price', 'sale', 'off'));
                    if ($start >= 0 && $end >= 0 && $start <= $end && $in_array) {
                        foreach ($mark_values as $key=>$value) {
                            $value = intval($value[$value_type_key]);
                            if ($value < $start || $value > $end) {
                                unset($mark_values[$key]);
                            }
                        }
                    }
                }

                $value_type_key = $activity_mark['value_type'] . '_mark';
                $mark_pic_values = Utilities::DataToArray($mark_values, $value_type_key);
                $activity_mark_pics = FashionGoodsMarks::getMarkPic(
                        array(
                            'mark_type' => $activity_mark[$mark_type_key],
                            'mark_value' => $mark_pic_values,
                            ), '*', false, 'mark_value'
                        );

                foreach ($mark_values as $gid => $mark_value){
                    $mark_pic = $activity_mark_pics[$mark_value[$value_type_key]];
                    if (empty($mark_pic) || !is_array($mark_pic)) {
                        continue;
                    }

                    $mark = array(
                            'mark_id' => "{$mark_id}",
                            'img_url' => Utilities::convertPicture($mark_pic['img_url']),
                            'img_width' => $mark_pic['img_width'],
                            'img_height' => $mark_pic['img_height']);
                    $show_marks[$gid] = array($mark);
                }
            }

        } while (FALSE);
        
        return $show_marks;
    }
}

/*
array(
   'mark_id' => '1999',
   'img_url' => $price_mark_img_url,
   'img_width' => '63',
   'img_height' => '83',
   'mark_text' => '￥' . $off['phone_price'],
   'text_font_color' => '#993300',
   'text_margin_top' => '56',
);
*/
