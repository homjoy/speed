<?php
namespace Libs\Util;

class ArrayUtilities {

    /** 
     * 返回数组中指定的一列
     */
    public static function my_array_column($array, $colName){

        $results=array();
        if(!is_array($array)) return $results;

        if (function_exists('array_column')) {
            return array_column($array, $colName);
        }

        foreach($array as $child){
            if(!is_array($child)) continue;
            if(array_key_exists($colName, $child)){
                $results[] = $child[$colName];
            }
        }
        return $results;
    }

    /**
     * 对二维数组进行排序
     * @param $array
     * @param $keyid 排序的键值
     * @param $order 排序方式 'asc':升序 'desc':降序
     * @param $type  键值类型 'number':数字 'string':字符串
     */
    public static function sort_array(&$array, $keyid, $order = 'asc', $type = 'number') {
        if (is_array($array)) {
            foreach($array as $val) {
                $order_arr[] = $val[$keyid];
            }

            $order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
            $type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;

            array_multisort($order_arr, $order, $type, $array);

            return $array;
        }
    }

    /**
     * 对二维数组进行排序，带key
     * @param $array
     * @param $keyid 排序的键值
     * @param $order 排序方式 'SORT_ASC':升序 'SORT_DESC':降序
     */
    public static function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    /** 
     * 根据参数拼成cache key
     */
    public static function genParamsCacheKey($params = array()) {

        if (empty($params)) {
            return '';
        }

        $return = '';
        foreach ($params as $key => $value){
            if (is_array($value)) {
                $value = self::genParamsCacheKey($value);
            }
            $return .= $key.':'.$value.':';
        }
        return rtrim($return, ":");
    }


    /**
     * 用指定字段将数组转换为哈希
     * @param $data
     * @param $hashKey
     * @param bool $toLower 字符串类型的字段是否转换大小写
     * @return array|bool
     */
    public static function hashByKey($data,$hashKey,$toLower = false)
    {
        $retArr = array ();
        if(empty($data)){
            return array();
        }
        if(empty($hashKey)){
            return $data;
        }
        
        foreach ( $data as $v ) {
            $k = $toLower ? strtolower($v[$hashKey]) : $v[$hashKey];
            $retArr[$k] = $v;
        }

        return $retArr;
    }

    /**
     * 以指定KEY 进行数据分组
     * @param $data
     * @param $groupKey
     * @param bool $toLower
     * @return array
     */
    public static function groupByKey($data,$groupKey,$toLower = false)
    {
        $retArr = array ();
        if(empty($data)){
            return array();
        }
        if(empty($groupKey)){
            return $data;
        }

        foreach ( $data as $v ) {
            $k = $toLower ? strtolower($v[$groupKey]) : $v[$groupKey];
            $retArr[$k][] = $v;
        }

        return $retArr;
    }

    /**
     * 构建子节点树
     * @param $data
     * @param $list
     * @param $parent
     * @return array
     */
    public static function buildSubTree(&$list, $parentColumn = 'parentid', $pkid = 'id', $subColumn = 'children'){

        if (empty($list)) {
            return $list;
        }

        $tree = array();
        foreach ($list as $v){
            if (isset($list[$v[$parentColumn]])) {
                $list[$v[$parentColumn]][$subColumn][$v[$pkid]] = &$list[$v[$pkid]];
            }else{
                $tree[$v[$pkid]] = &$list[$v[$pkid]];
            }
        }
        return $tree;
    }

    /**
     * 构建父节点
     * @param $data
     * @param $list
     * @param $parent
     * @return array
     */
    public static function buildSupTree(&$list, $parentColumn = 'parentid', $pkid = 'id', $supColumn = 'parents'){

        if (empty($list)) {
            return $list;
        }

        $tree = array();
        foreach ($list as $v){
            if (isset($list[$v[$parentColumn]])) {
                $list[$v[$pkid]][$supColumn][] = &$list[$v[$parentColumn]];
            }
            $tree[$v[$pkid]] = &$list[$v[$pkid]];
        }
        return $tree;
    }

    /**
     * 合并分类为二维数据
     * @param $data
     * @param $list
     * @param $parent
     * @return array
     */
    public static function arrangeTree($list, $arrangeColumn = 'parents'){

        if (empty($list)) {
            return $list;
        }

        $tree = array();
        if (isset($list[$arrangeColumn])) {
            $sub = $list[$arrangeColumn];
            unset($list[$arrangeColumn]);
            $tree = self::arrangeTree(current($sub));
        }
        $tree[] = $list;
        return $tree;
    }



}
