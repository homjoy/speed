<?php
namespace Libs\Serviceclient;
/**
 * @author
 */


class Transport {

    public static $multiRequestType = 'multicurl';

    public static function changeMultiRequestMethod($type = '') {
        if (in_array($type, array('batch', 'multicurl'))) {
            self::$multiRequestType = $type; 
        }
    }

    public static function exec($requestList = array(), $opt= array()) {
        if(empty($requestList))
            return array();
        foreach ($requestList as $request) {
            $opt = self::combinOptions($request->opt, $opt);
            $request->setOptions($opt);
        }
        if (count($requestList) > 1) {
            $responseList = self::multiRequest($requestList, self::$multiRequestType);
        }
        else {
            $responseList = self::curlExec($requestList); 
        }
        return $responseList;
    }

    private static function combinOptions($opt, $additionOpt) {
        if (!is_array($additionOpt)) {
            return $opt;    
        }
        foreach ($additionOpt as $type => $value) {
            if (isset($opt[$type])) {
                $opt[$type] = $value;
            }
            else {
                $opt[$type] = $value;
            }
        }
        return $opt; 
    }

    private static function multiRequest($requestList, $type) {
        if (empty($requestList)) {
            return array(); 
        }
        $responseList = self::MultiCurlExec($requestList);
        return $responseList; 
    }

    private static function MultiCurlExec($requestList) {
        MultiCurl::instance()->open();
        MultiCurl::instance()->send($requestList);
        $responseList = MultiCurl::instance()->exec();
        MultiCurl::instance()->close();
        return $responseList;       
    }

    private static function curlExec($requestList) {
        $responseList = array();
        foreach ($requestList as $key => $request) {
            Curl::instance()->open();
            Curl::instance()->send($request);
            $responseList[$key] = Curl::instance()->exec();
            Curl::instance()->close();
        }
        return $responseList;
    }
}

