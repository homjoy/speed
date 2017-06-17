<?php
namespace Apicloud\Modules\Page;

class DownloadApp extends \Frame\Module {

	public function run() {

		$agent 		= self::getAgent();
		$version   	= self::getLatestVersion($agent);
		$url   		= self::getDownloadUrl($agent, $version);

		$return = array(
	        'version'	=> $version,
	        'url'		=> $url,
	    );

		$this->app->response->setBody($return);
	}

	//检测 USER_AGENT
	public static function getAgent(){

		$agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
		$iphone = (strpos($agent, 'iphone')) ? true : false; 
		$ipad = (strpos($agent, 'ipad')) ? true : false; 
		$android = (strpos($agent, 'android')) ? true : false;

		if($iphone || $ipad)
		{
			return 'ios';
		}
		elseif($android)
		{
			return 'android';
		}
		else
		{
			return 'unknown';
		}
	}

	//获取最新版本
	public static function getLatestVersion($agent = ''){

		switch ($agent) {
			case 'ios':
				$version = 1;
				break;
			case 'android':
				$version = 1;
				break;
			default:
				$version = 1;
				break;
		}
		return $version;
	}

	//下载链接
	public static function getDownloadUrl($agent = '', $version = ''){

		switch ($agent) {
			case 'ios':
				$url = 'itms-services://?action=download-manifest&url=https://mlstest.b0.upaiyun.com/speed.plist?'.$version;
				break;
			case 'android':
				$url = 'http://apk.m.com/OA/MeilishuoOA.apk?'.$version;
				break;
			default:
				$url = '#';
				break;
		}
		return $url;
	}
}
