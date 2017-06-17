<?php

namespace Libs\Util;

/**
 * 输出数据格式化
 * @author hepang@meilishuo.com
 * @since 2015-03-27
 */

class Format {

	//格式化
	public static function outputData($data=array(), $format=array(), $multi = FALSE) {

		if (empty($format) || empty($data)) {
			return $data;
		}

		if ($multi) {
			foreach ($data as $key => $value) {
				$data[$key] = self::intersectArray($value, $format);
			}
		}else{
			$data = self::intersectArray($data, $format);
		}

        return $data;
	}

	//单条数据替换
	public static function intersectArray($data=array(), $format=array()) {

		if (empty($format) || empty($data)) {
			return $data;
		}

        $data = array_intersect_key($data, $format);
        $data = array_merge($format, $data);

        //统一类型
        foreach ($format as $key => $value) {

        	if (is_string($value)) {
        		$data[$key] = (string)$data[$key];
        	}elseif (is_numeric($value)) {
        		$data[$key] = $data[$key]+0;
        	}elseif (is_bool($value)) {
        		$data[$key] = (bool)$data[$key];
        	}elseif (is_object($value)) {
        		$data[$key] = (object)$data[$key];
        	}else {
        		# code...
        	}

        }

        return $data;
	}

}







