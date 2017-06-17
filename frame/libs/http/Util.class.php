<?php
namespace Libs\Http;

class Util {

	public static function parseRequestHeaders() {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		return $headers;
	}


	public static function unmarkAmps($data) {
        if (empty($data)) return $data;

        $new_data = array();
        foreach ($data as $key => $value) {
            if (preg_match('/^amp\;(.*)$/i', $key)) {
                $new_key = preg_replace('/^amp\;(.*)$/i', '$1', $key);
                $new_key != '' && $new_data[$new_key] = $value;
            } else {
                $new_data[$key] = $value;
            }
        }
		return $new_data;
	}

	public static function slashes($data) {
        return is_array($data) ? array_map(array('self', 'slashes'), $data) : htmlspecialchars(stripslashes(self::utf8Encode($data)));
	}

    public static function utf8Encode($string) {
        !mb_check_encoding($string, 'UTF-8') && $string = @mb_convert_encoding($string, 'UTF-8', 'ascii,GB2312,gbk,UTF-8');
        return $string;
    }

}
