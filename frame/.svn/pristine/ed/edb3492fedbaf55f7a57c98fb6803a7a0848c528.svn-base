<?php

namespace Libs\Serviceclient;

/* curl基类
 * @package Base
 * @author weiwang
 * @since 2012.12.17
 * @统计超时日志数量的shell例子: for i in `ls -d mutilcurl_* | grep -v "result"`;do grep "2013-12-16" $i/$i\_current  | wc -l;echo $i\_current;done
 */

abstract class CurlBase {

    /**
     * 记录错误日志
     *
     * @return boole 
     * @access private
     * @param $ch curl的句柄,$file要写入的日志文件，$url 请求的url
     */
    public function wlog($ch, $file) {
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        if ($code != 200 || $curlErrno > 0) {
            //很多400都是工程师抛出来的，所以不纪录日志了
            if ($code == 400) {
                return TRUE;               
            }
            
            if ($code == 404 && $curlErrno == 0) {
                return TRUE;
            }               

            $info = curl_getinfo($ch);

            $time = $_SERVER['REQUEST_TIME'];

            $hostname = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : exec("hostname");
            $fromIp = gethostbyname($hostname);
            //$fromIp = gethostbyname($_SERVER['HOSTNAME']);

            $uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];

            $str = "[" . $time . "]\t" . "[" . $fromIp . "]\t" . "[" . $url . "]\t" . "[error:curl_errno=" . $curlErrno . ": curl_error = $curlError ]\t" . "[code=" . $code . "]";
            $str .= "[filetime=" . $info['filetime'] . "]\t[total_time=" . $info['total_time'] . "]\t[namelookup_time=" . $info['namelookup_time'] . "]\t[connect_time=" . $info['connect_time'] . "]\t[pretransfer_time=" . $info['pretransfer_time'] . "]\t[redirect_time=" . $info['redirect_time'] . "]\t[size_download=" . $info['size_download'] . "]\t[speed_download=" . $info['speed_download'] . "]\t[starttransfer_time=" . $info['starttransfer_time'] . "]\t[{$uri}]";

            //TODO
            //$logHandler = new \Snake\Libs\Base\SnakeLog($file, 'normal');
            //$logHandler->w_log($str);
        }
        return TRUE;
    }
}
