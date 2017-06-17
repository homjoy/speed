<?php

namespace Joint\Scripts\Punch;

use Frame\Speed\Lib\Api;

date_default_timezone_set('Asia/Shanghai');
/**
 * 门卡：推送部门数据(增量更新) 给门禁卡系统
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class PushDepartInfo extends \Frame\Script
{
    const PUNCH_URL = 'http://172.17.16.2:8009';
    //const PUNCH_URL = 'http://honoo.eicp.net:8095';
    //删除需要的字段
    protected $delete_fields = array(
        'code' => 'depart_id'
    );
    //增加需要的字段
    protected $field = array(
        'code' => 'depart_id',
        'name' => 'depart_name',
        'parentcode' => 'parent_id',
        'remark' => 'depart_info',
        'root' => 'parent_id',
    );
    //更改需要的字段
    protected $update_field = array(
        'code' => 'depart_id',
        'name' => 'depart_name',
        'parentcode' => 'parent_id'
    );

    /*
     *  code	部门编号code	部门编号
      name	部门名称
      parentcode	上级部门编号
      remark	备注
      root	是否是根节点(true –是,false –否)
      name	部门名称
      parentcode	上级部门编号
      remark	备注
      root	是否是根节点(true –是,false –否)
     *
     */

    public function run() {
        //获取部门的需要同步的数据
        $params = array(
            'all' => 1,
            'status' => array(0, 9),
            'channel' => 2,
        );
        $depart_queue_info = APi::atom('punch/get_punch_syn_info', $params);
        if (empty($depart_queue_info)) {
            echo '['.date('Y-m-d H:i:s').']'.' 无数据'."\r\n";
            exit;
        }
        //循环所有数据
        foreach ($depart_queue_info as $depart_key => $depart_content) {
            $depart_value = json_decode($depart_content['content'], true);
            switch ($depart_value['status']) {
                case 0: //删除
                    $return = $this->pushDepartInfo($depart_value, $this->delete_fields, 'delete');
                    $return['code'] = $depart_value['depart_id'];
                    $return['msg'] = "del:";
                    break;
                case 1: //修改
                    //判断父亲节点是否存在
                    $tmp_depart['depart_id'] = $depart_value['parent_id'];
                    //判断父部门存在
                    if(!$this->getDepartInfo($tmp_depart) && $tmp_depart['depart_id']!=0) {
                        $return['success'] = false;
                    }else {
                        if ($this->getDepartInfo($depart_value)) {
                            $return = $this->pushDepartInfo($depart_value, $this->update_field, 'update');
                            $return['code'] = $depart_value['depart_id'];
                            $return['msg'] = "update" ;
                        } else {//增加
                            $return = $this->pushDepartInfo($depart_value, $this->field, 'add');
                            $return['code'] = $depart_value['depart_id'];
                            $return['msg'] = "add";
                        }
                    }
                    break;
            }
            $update_params['id'] = array($depart_content['id']);
            $update_params['send_at'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            if (isset($return['success']) && $return['success'] == true) {
                $update_params['status'] = 10;
            } else {
                $update_params['status'] = 9;
            }
            $msg = APi::atom('punch/update_punch_syn_info', $update_params);
            echo '['.date('Y-m-d H:i:s').']更新的数据' . json_encode($update_params) . ';更新队列返回值:' . $msg . '发送数据返回值' . json_encode($return)."\r\n";
        }
    }

    /**
     * 同步部门数据，增删该查
     *
     * @param array $param
     *
     * @return bool
     */
    protected function pushDepartInfo($param = array(), $fields = array(), $method = "delete") {
        //return array('success' => true);
        if (empty($param)){
            return array();
        }
        $post_url = self::PUNCH_URL . '/api/department';
        $data = $this->format_param($param, $fields, $method);
        $this->app->log->log('crontab/punch_push_depart_info_log_info', json_encode($data));
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->post($post_url, json_encode($data), true);
        $body = json_decode($ret['body'], true);
        return $body;
    }

    /**
     * 整理数据
     *
     * @param array  $params
     * @param array  $fields 数据需要的字段 和数组的对应关系
     * @param string $method 方法 add get delete update
     *
     * @return array
     */
    protected function format_param($params = array(), $fields = array('code' => 'depart_id'), $method = 'get') {
        if (empty($params)) {
            return array();
        }
        $new_params = array(
            'datatype' => 'department',
            'optype' => $method,
            "key" => "mls-iwer13209ud8yr32oih928y43rt"
        );

        $tmp_params = array();
        foreach ($fields as $field_key => $field_value) {
            if ($field_key == 'root') {
                $tmp_params['root'] = (isset($params['parent_id']) && $params['parent_id'] == 0) ? true : false;
            } else {
                $tmp_params[$field_key] = isset($params[$field_value]) ? $params[$field_value] : '';
            }
        }
        $new_params['data'] = $tmp_params;

        return $new_params;
    }

    /**
     * 查询部门是否存在,存在调用 update else 调用 add
     *
     * @param array $params
     *
     * @return bool
     */
    public function getDepartInfo($params = array()) {
        if (empty($params)) {
            return false;
        }
        $code = array('code' => isset($params['depart_id']) ? $params['depart_id'] : '');
        $post_url = self::PUNCH_URL . '/api/department';
        $this->url = $post_url . '?' . http_build_query($code);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($this->url);
        $body = $ret['body'];
        $body = json_decode($body, true);
        if (!empty($body['data'])) { //如果不为空 返回真
            return true;
        } else {
            return false;
        }
    }

}
