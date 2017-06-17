<?php
namespace Libs\Util;


class Platform {
	
	public static function getPlatform() {
		$userAgent = $_SERVER ['HTTP_USER_AGENT'];
		if (empty ( $userAgent )) {
			return 'Other';
		}
		if (FALSE !== stripos ( $userAgent, 'iphone' )) {
			return 'iPhone';
		}
		if (FALSE !== stripos ( $userAgent, 'ipad' )) {
			return 'iPad';
		}
		if (FALSE !== stripos ( $userAgent, 'ipod' )) {
			return 'iPod';
		}
		if (FALSE !== stripos ( $userAgent, 'android' )) {
			return 'Android';
		}
		return 'PC';
	}
}
