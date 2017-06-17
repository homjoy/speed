<?php

namespace Worker\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');
/**
 *  抓取门禁卡系统的打卡记录，保存到队列表
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class CrawlPunchLog extends \Frame\Script
{
    protected $params = array();
    protected $errors = array();
    const PUNCH_URL = 'http://172.17.16.2:8009';
    //const PUNCH_URL = 'http://honoo.eicp.net:8095';
    public function run()
    {
        //1.时间
//        $max_create_time = Api::atom('punch/get_max_punch_crawl_log',array());
//        if(!empty($max_create_time)) {
//            $max_create_time = array_pop($max_create_time);
//            $starttime = $max_create_time['create_time'];
//        }else{
//            //$starttime = '2015-10-09 09:55:00';
//            $starttime = date('Y-m-d H:i:s', strtotime('-5 minute'));
//        }
//        
//        $endtime = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
//        $args = $this->request->arg['arg'];
//        if(!empty($args[0])){
//            $starttime = $args[0];
//        }
//        if(!empty($args[1])){
//            $endtime = $args[1];
//        }
        //检测数据，获取当前时间前一天的数据
        $starttime = date('Y-m-d 00:00:00',strtotime('-1 day'));
        $endtime = date('Y-m-d 23:59:59',strtotime('-1 day'));
        $search_params = array('startdate' => $starttime, 'enddate' => $endtime);
        $punch_log_info = $this->getPunchLog($search_params);
        $count_num = count($punch_log_info);
        echo '['.date('Y-m-d H:i:s').']抓取时间：'.$starttime.'到'.$endtime.'，共获取'.$count_num."\r\n";
        if (!empty($punch_log_info)) {
            //获取所有的页数
            foreach($punch_log_info as $key =>$punch_value) {
                $post_param = array(
                    'content' => base64_encode(json_encode ($punch_value)),
                    'status'  => 0,
                    'create_time' =>$endtime,
                    'identify' => md5($punch_value['code'].$punch_value['cardno'].$punch_value['checktime'])
                );
                $return_info = Api::atom('punch/create_punch_crawl_log',$post_param);
                if(intval($return_info) >0 ) {
                    //echo '['.date('Y-m-d H:i:s').']无数据返回'."\r\n";
                }else{
                    //失败，记录错误日志
                    $error_params = array(
                        'content' => json_encode ($punch_value),
                        'status'  => 0,
                        'create_time' =>$endtime,
                        'identify' => md5($punch_value['code'].$punch_value['cardno'].$punch_value['checktime'])
                    );
                    $error_sql = $this->createSql($error_params);
                    echo '['.date('Y-m-d H:i:s').']'.'@'.$error_sql."\r\n";
                }
            }
        } else {
            echo '['.date('Y-m-d H:i:s').']无数据返回'."\r\n";
        }
        echo '['.date('Y-m-d H:i:s').']END'."\r\n";
    }

    /**
     * 创建失败的sql
     * @param array $params
     *
     * @return string
     */
    public function createSql($params = array()){
        if(empty($params)) return '';
        $sql =" INSERT INTO `punch_crawl_log` (`content`,`status`,`create_time`) VALUES";
        $sql.="('".$params['content']."','".$params['status']."','".$params['create_time']."')";
        return $sql;
    }
    /**
     * 获取打卡日志
     *
     * @param array $params
     *
     * @return bool
     */
    public function getPunchLog($params = array())
    {
        if (empty($params)) {
            return false;
        }
        $post_url = self::PUNCH_URL . '/api/record';
        $this->url = $post_url . '?' . http_build_query($params);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($this->url);
        $body = json_decode($ret['body'], true);
        if (!empty($body['data'])) {
            return $body['data'];
        } else {
            return array();
        }
    }


}
