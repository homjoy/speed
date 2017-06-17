<?php
namespace Apicloud\Package\Common;

class Utilities {

	public static function post2Str() {
		$retStr = "";
		foreach ($_POST as $k => $v) {
			$k = urlencode($k);
            if (!is_array($v)) {
                $v = array($v);
            }
            $postValue = '';
            foreach ($v as $item) {
                if (!is_array($item)) {  
                    $postValue .= urlencode($item) . '|';
                } else {
                    $postValue .= json_encode($item) . '|';                    
                } 
            }
			$postValue = rtrim($postValue, '|');
			$retStr .= "{$k}:{$postValue},";
		}
		return $retStr;
	}

	public static function platform($client_id) {
		$platform = 'Unkown';
		if (!empty($client_id)) {
			if ($client_id <= 100) {
				$platform = 'iPhone';
			}
			elseif ($client_id >= 2001 && $client_id < 3000) {
				$platform = 'iPad';
			}
			elseif ($client_id >= 5001 && $client_id <= 10000) {
				$platform = 'iPhoneSub';
			}
			elseif ($client_id >= 10001 && $client_id <= 90000) {
				$platform = 'Android';
				// for hack 30320-30329为MeilishuoTV
				if ($client_id >= 30320 && $client_id <= 30329) {
					$platform = 'MeilishuoTV';
				}
			}
			else {
				$platform = 'WindowsPhone';
			}
		}
		return $platform;
	}
	
	public static function deviceId($request) {
		$deviceId = $request->REQUEST['mac'];
		empty($deviceId) && $deviceId = $request->REQUEST['udid'];
		empty($deviceId) && $deviceId = $request->REQUEST['imei'];
		return $deviceId;
	}

    public static function allDeviceMark($request) {
        return array(
            'mac' => $request->REQUEST['mac'],
            'udid' => $request->REQUEST['udid'],
            'imei' => $request->REQUEST['imei'],
            'ip' => $request->ip,
        );
    }

    public static function deviceInfo() {
        if (!empty($_SERVER['HTTP_DEVICE_INFO'])) {
            $device_info = json_decode($_SERVER['HTTP_DEVICE_INFO'], TRUE);
            return $device_info;
        } 
        return array();
    }
	
	public static function generateSessionId() {
		return md5($_SERVER['REQUEST_TIME'] . '-' . rand());
	}
	
	/**
	 * 重新初始化platform
	 * 互联用到
	 * wap可能会用到
	 */
	public static function reinitPlatform($agent, $platform = 'Unkown') {
		if ('Unkown' != $platform) {
			return $platform;
		}
		if (stripos($agent, 'iphone') !== FALSE) {
			$platform = 'iPhone';
		} elseif (stripos($agent, 'ipad') !== FALSE) {
			$platform = 'iPad';
		} elseif (stripos($agent, 'android') !== FALSE) {
			$platform = 'Android';
		}
		return $platform;
	}
	
	/**
	 * web的snake不负责跳转
	 * for hack
	 * 无线互联需要跳转
	 */
	public static function goToUrl($destUrl) {
		$destUrl = trim(htmlspecialchars_decode($destUrl));
		if (stripos($destUrl, "http://") === FALSE && stripos($destUrl, "https://") === FALSE) {
			if (stripos($destUrl, "meilishuo.com") === FALSE) {
				$destUrl = MEILISHUO_URL . $destUrl;
			} else {
				$destUrl = "http://" . $destUrl;
			}
		}
		
		echo "<script>
			function goURL( goUrlStr ) {
				if ( typeof(goUrlStr) != 'undefined') {
					var isIe=(document.all)? true : false;
					if(isIe) {
						var linka = document.createElement('a');
						linka.href = goUrlStr;
						document.body.appendChild(linka);
						linka.click();
					} else {
						window.location = goUrlStr;
					}
		    	}
		    	return true;
			}
			</script>
			<body><script>goURL('{$destUrl}');</script></body>";
	}

    public static function fixNickname($nickname) {
        $nickname = trim($nickname);
        if (mb_strpos($nickname, '#', 0, 'utf-8') > 0) {
            $nicknameArr = explode ('#', $nickname);
            if (isset($nicknameArr[0])) {
                return $nicknameArr[0];
            } else {
                return $nickname;
            }
        } else {
            return $nickname;
        }  
    }

    public static function strvalArray($array) {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::strvalArray($value);
            } else {
                $value = strval($value);
            } 
        }
        return $array;
    }

    /**
     * Process atme, emotion and topic.
     */
    public static function replaceSpecialMarks($input) {
        $pattern = array(
            '/\[(.+?)\]/m',
            '/@([^ ^@^:]+)/m',
            '/#(.+?)#/m',
            '/(https?:\/\/[\w\d:#@%\/;$()~_?+-=\\\.&]*)/m',
        );

        $replacement = array(
            '[$1]',
            '<atme|$1>',
            '<topic|$1>',
            '<link|$1>',
        );

        return preg_replace($pattern, $replacement, $input);
    } 


    /**
     * Process HTML content of twitter for API using.
     */
    public static function convertHTMLContent($htmlContent) {
        $tmpArr = explode("<br/>" , $htmlContent );
        $htmlContent = html_entity_decode(strip_tags(trim($tmpArr[0])), ENT_COMPAT, 'UTF-8');
        return str_replace("\r\n", ' ', $htmlContent);
    }

    /** 
     * 格式化url
     */
	public static function formatPictureUrl($url = '') {
		if (empty($url)) {
			return 'http://speed.meilishuo.com/static/avatar/avatar_default.jpeg';
		}
		if (strpos($url, 'http://') !== FALSE){
			return $url;
		}else{
			//return 'http://124.202.144.24/' . $url;
			return 'http://imgst.meilishuo.net/' . $url;
		}
	}

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
     * 无限分类数据树形格式化
     */
    public static function genTree($items = array(), $id = '', $upid = '') {

        if (empty($items) || empty($id) || empty($upid)) {
            return $items;
        }

        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item[$upid]]))
                $items[$item[$upid]]['sub_depart'][] = &$items[$item[$id]];
            else
                $tree[] = &$items[$item[$id]];
        return $tree;
    }

    /**
     * 生成树
     */
    public static function genDepartTreeArray($tree = array(), $pre = '') {

        if (empty($tree)) {
            return array();
        }

        $return = array();

        foreach ($tree as $k => $v) {
            if (!empty($v)) {

                if (isset($v['son'])) {
                    $v['depart_name'] = $pre.$v['depart_name'];
                    $son = $v['son'];
                    unset($v['son']);
                    $return[] = $v;

                    $tmp = self::genDepartTreeArray($son, $pre.'---');
                    if (is_array($tmp)) {
                        foreach ($tmp as $key => $value) {
                            $return[] = $value;
                        }
                    }
                }
                else{
                    $v['depart_name'] = $pre.$v['depart_name'];
                    $return[] = $v;
                }
            }
        }

        return $return;
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
     * 获取所有子id
     */
    public static function genChildIds($items = array()) {

        if (empty($items)) {
            return $items;
        }

        $tree = array(); //格式化好的树
        foreach ($items as $item){
            if (isset($item['sub_depart'])){
                $sub1 = $item['sub_depart'];
                unset($item['sub_depart']);
                $tree[$item['depart_id']][] = $item;
                foreach ($sub1 as $sub1value) {
                    if (isset($sub1value['sub_depart'])){
                        $sub2 = $sub1value['sub_depart'];
                        unset($sub1value['sub_depart']);
                        $tree[$sub1value['depart_id']][] = $sub1value;
                        $tree[$item['depart_id']][] = $sub1value;

                        foreach ($sub2 as $sub2value) {
                            if (isset($sub2value['sub_depart'])) {
                                $sub3 = $sub2value['sub_depart'];
                                unset($sub2value['sub_depart']);
                                $tree[$sub2value['depart_id']][] = $sub2value;
                                $tree[$sub1value['depart_id']][] = $sub2value;
                                $tree[$item['depart_id']][] = $sub2value;
                            }else{
                                $tree[$sub2value['depart_id']][] = $sub2value;
                                $tree[$sub1value['depart_id']][] = $sub2value;
                                $tree[$item['depart_id']][] = $sub2value;
                            }
                        }
                    }else{
                        $tree[$sub1value['depart_id']][] = $sub1value;
                        $tree[$item['depart_id']][] = $sub1value;
                    }
                }
            }
            else{
                $tree[$item['depart_id']][] = $item;
            }
        }
        return $tree;
    }

    /** 
     * 检测缓存返回数据
     */
    public static function formatDataFromMemcache($data = '') {

        if (empty($data)) {
            return '';
        }

        if (!is_array($data)) {
            return json_decode($data);
        }
        return $data;
    }

    /**
     * 获取星期
     * @param $week
     * @return $result
     */
    public static function getWeek($week = 0){
        $array = array(
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
            7 => '周日',
        );
        return $array[$week];
    }

    /**
     * 获取预定会议室重复
     * @param $week
     * @return $result
     */
    public static function getRepeat($week = 0){
        $repeat = array(
            '0'=> '无',
            '1'=> '每天',
            '7'=> '每周',
            '30'=> '每月',
        );
        return $repeat[$week];
    }

    /**
     *
     * 二维数组按照嵌套中的一维数组的某个字段去除重复数组
     * @author haibinzhou@meilishuo.com
     * @data 2015-09-09
     * @params  $data 二维数组
     * @params  id    字符串
     *
     */
    public static function drop_repeat($data,$id){
        $arr_id = array();
        $arr_data = array();
        foreach($data as $key=>$value){
            if($key==0){
                $arr_id[] = $value[$id];
                $arr_data[] = $value;
            }else{
                if(!in_array($value[$id],$arr_id)){
                    $arr_id[] = $value[$id];
                    $arr_data[] = $value;
                }
            }

        }
        return $arr_data;
    }



}
