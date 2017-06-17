<?php
namespace Apicloud\Package\Common;

/**
 * 基础类
 * Class BasePackage
 * @package Apicloud\Package\Common
 */

abstract class BasePackage {

    public static function getClient()
    {
        static $client = null;
        if (is_null($client)) {
            $client = new \Libs\Serviceclient\Client;
        }
        return $client;
    }
    
    public static function getCurlObj() {
    	static $curl_obj = NULL;
    	if(is_null($curl_obj)) {
    		$curl_obj = new \Libs\Sphinx\curl;
    	}
    	return $curl_obj;
    }
    
}
