<?php
namespace Libs\Mark;

class DBFashionGoodsMarksHelper extends \Libs\DB\DBConnManager {
    const _DATABASE_ = 'coral';
}

class FashionGoodsMarks {
    private static $goods_mark_table = 't_coral_goods_mark';
    private static $attr_mark_table = 't_coral_attr_mark';
    private static $mark_info_table = 't_coral_mark_info';
    private static $mark_pic_table = 't_coral_mark_pic';
    private static $mark_detail_table = 't_coral_mark_detail';

    public static function getGoodsMark($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (empty($params)) {
            return FALSE;
        }

        $selectSql = "SELECT $cols FROM " . self::$goods_mark_table  . " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $current_time = date('Y-m-d H:i:s', time());
        $selectSql .= " AND `start_time` < " . "'" . $current_time . "'" . " AND (`end_time` > " . "'" . $current_time . "' OR `end_time` = '0000-00-00 00:00:00')";
        $goodsMarkInfo = DBFashionGoodsMarksHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $goodsMarkInfo;
    }

    public static function getMark($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (isset($params['data_id']) && isset($params['type'])) {
            $table = self::$attr_mark_table;
        } else if(isset($params['goods_id'])) {
            $table = self::$goods_mark_table;
        } else {
            return FALSE;
        }

        $selectSql = "SELECT $cols FROM " . $table . " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $current_time = date('Y-m-d H:i:s', time());
        $selectSql .= " AND `start_time` < " . "'" . $current_time . "'" . " AND (`end_time` > " . "'" . $current_time . "' OR `end_time` = '0000-00-00 00:00:00')";
        $goodsMarkInfo = DBFashionGoodsMarksHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $goodsMarkInfo;
    }

    public static function getMarkInfo($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (empty($params)) {
            return FALSE;
        }

        $selectSql = "SELECT $cols FROM " . self::$mark_info_table  . " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $marksInfo = DBFashionGoodsMarksHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $marksInfo;
    }
    
    public static function getMarkPic($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (empty($params)) {
            return FALSE;
        }
    
        $selectSql = "SELECT $cols FROM " . self::$mark_pic_table. " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $marksInfo = DBFashionGoodsMarksHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $marksInfo;
    }

    public static function getMarkDetail($params, $cols = "*", $fromMaster = FALSE, $hashKey = NULL) {
        if (empty($params)) {
            return FALSE;
        }

        $selectSql = "SELECT $cols FROM " . self::$mark_detail_table  . " WHERE 1 = 1";
        $sqlData = array();
        foreach ($params as $paramKey => $paramValue) {
            if (is_array($paramValue)) {
                $selectSql .= " AND `$paramKey` IN ('" . implode("','", $paramValue) . "')";
            } else {
                $selectSql .= " AND `$paramKey` = :$paramKey";
                $sqlData[$paramKey] = $paramValue;
            }
        }
        $markDetailInfo = DBFashionGoodsMarksHelper::getConn()->read($selectSql, $sqlData, $fromMaster, $hashKey);
        return $markDetailInfo;
    }
}
