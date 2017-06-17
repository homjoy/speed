<?php

namespace Joint\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');
/**
 * 解析门禁卡系统中，抓取到的门禁卡记录
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class SavePunchLog extends \Frame\Script
{
    protected $params = array();
    protected $errors = array();
    protected $page_size = 100;

    public function run()
    {
        //将所有状态为 9的数据更为 0
        $update_crawl_params = array(
            'condition' =>array('status' => 9),
            'params' => array('status' => 0)
        );
        $update_return= APi::atom('punch/update_punch_crawl_log_status', $update_crawl_params);
        $params = array(
            'status' => 0,
        );
        $total_params = $params;
        $total_params['count'] = 1;
        $total = APi::atom('punch/get_punch_crawl_log', $total_params);
        echo '['.date('Y-m-d H:i:s').'] 共获取'.$total."\r\n";
        if($total <=0) {
             echo '['.date('Y-m-d H:i:s').'] NO DATA'."\r\n";
        }
        $page_num = ceil($total/$this->page_size);
        $params['limit'] = $this->page_size;
        for($i = 0;$i < $page_num;$i++) {
            $params['offset'] = 0;
            $punch_info = APi::atom('punch/get_punch_crawl_log', $params);
            //循环所有数据
            if (!empty($punch_info)) {
                foreach ($punch_info as $punch_key => $punch_value) {
                    $content_value = json_decode($punch_value['content'],true);
                    $tmp_value = array(
                        'user_id'    => 0,
                        'staff_id'   => $content_value['code'],
                        'user_name'  => $content_value['name'],
                        'card_number'=> $content_value['cardno'],
                        'event_time' => date('Y-m-d H:i:s',strtotime($content_value['checktime'])),
                    );

                    $return = APi::atom('punch/create_punch_log', $tmp_value);
                   
                    $update_params = array('id' => $punch_value['id']);
                    if(intval($return) >0) {
                        $update_params['status'] = 10;
                    }else{
                        $update_params['status'] = 9;
                    }
                    $update_return = APi::atom('punch/update/punch_crawl_log', $update_params);
                  //  $this->app->log->log('crontab/punch/update_punch_crawl_log_status_return','add：'.json_encode($return).'；update：'.json_encode($update_return).'；update_params：'.json_encode($update_params));
                    echo '['.date('Y-m-d H:i:s').'] add：'.json_encode($return).'；update：'.json_encode($update_return).'；update_params：'.json_encode($update_params). "\r\n";
                }
            }else{
                break;//如果值为空了，则结束循环
            }
        }
        echo '['.date('Y-m-d H:i:s').'] END'. "\r\n";
       
    }




}
