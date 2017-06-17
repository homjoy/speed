<?php 
namespace Libs\Mark;

//config for image host
$GLOBALS['PICTURE_DOMAINS'] = array(
		'a' => 'http://imgtest.meiliworks.com', //tx
		'b' => 'http://d04.res.meilishuo.net', //tx
		'c' => 'http://imgtest-dl.meiliworks.com', //dl
		'd' => 'http://d06.res.meilishuo.net', //dl
		'e' => 'http://d05.res.meilishuo.net', //ws
		'f' => 'http://d01.res.meilishuo.net', //ws
		'g' => 'http://d02.res.meilishuo.net', //tx
		'h' => 'http://d03.res.meilishuo.net', //tx
);

$GLOBALS['PICTURE_DOMAINS_ALLOCATION'] = 'aaddbbgggggggggggghhhhhhhhhhhhbbbbbbbbbbccddddddddggdddddddddddddddddeeeeeeeeeeeeeeeeeeeeeeeeeedddff';

class Utilities {

    public static function DataToArray($dbData, $keyword, $allowEmpty = FALSE) {
        $retArray = array ();
        if (is_array ( $dbData ) == false or empty ( $dbData )) {
            return $retArray;
        }
        foreach ( $dbData as $oneData ) {
            if (isset ( $oneData [$keyword] ) and empty ( $oneData [$keyword] ) == false or $allowEmpty) {
                $retArray [] = $oneData [$keyword];
            }
        }
        return $retArray;
    }

    public static function changeDataKeys($data, $keyName, $toLowerCase=false) {
        $resArr = array ();
        if(empty($data)){
            return false;
        }
        foreach ( $data as $v ) {
            $k = $v [$keyName];
            if( $toLowerCase === true ) {
                $k = strtolower($k);
            }
            $resArr [$k] = $v;
        }
        return $resArr;
    }

    public static function convertPicture($key) {

        if (strncasecmp($key, 'http://', strlen('http://')) == 0) {
            return $key;
    }

    $key = ltrim($key, '/');
    $hostPart = self::getPictureHost($key);
    if (empty($key)) {
        return $hostPart . '/css/images/0.gif';
    }
    return $hostPart . '/' . $key;
    }

    private static function getPictureHost($key) {
        if (empty($key)) {
            return $GLOBALS['PICTURE_DOMAINS']['a'];
        }
        if (substr($key, 0, 3) === 'css' && defined('CSS_JS_BASE_URL')) {
            return rtrim(CSS_JS_BASE_URL, '/');
        }
        $remain = crc32($key) % 100;
        $remain = abs($remain);
        $hashKey = $GLOBALS['PICTURE_DOMAINS_ALLOCATION'][$remain];
        return $GLOBALS['PICTURE_DOMAINS'][$hashKey];
    }

    public static function sortArray($array, $order_by, $order_type = 'ASC') {
        if (!is_array($array)) {
            return array();
        }
        $order_type = strtoupper($order_type);
        if ($order_type != 'DESC') {
            $order_type = SORT_ASC;
        } else {
            $order_type = SORT_DESC;
        }

        $order_by_array = array ();
        foreach ( $array as $k => $v ) {
            $order_by_array [] = $array [$k] [$order_by];
        }
        array_multisort($order_by_array, $order_type, $array);
        return $array;
    }

}
