<?php

namespace Joint\Scripts\Punch;

use Frame\Speed\Lib\Api;
date_default_timezone_set('Asia/Shanghai');
/**
 * 推送用户数据(增量更新)给门禁卡系统
 *
 * @author guojiehzu@meilishuo.com
 * @since  2015-10-16
 */
class PushUserInfo extends \Frame\Script
{
    protected $params = array();
    protected $errors = array();
    protected $delete_fields = array('code' => 'user_id');
    //更改和增加需要的字段
    protected $field = array(
        'code' => 'staff_id',
        'name' => 'name_cn',
        'deptcode' => 'depart_id',
        'cardno' => 'no',//这个不需要传，但是必须带这个参数
        'hiredate' => 'hire_time',
    );
    protected $update_field = array(
        'code' => 'staff_id',
        'name' => 'name_cn',
        'deptcode' => 'depart_id',
        'cardno' => 'no',
        'hiredate' => 'hire_time'
    );

    const PUNCH_URL = 'http://172.17.16.2:8009';

    public function run() {
        //获取队列数据
        $params = array(
            'all' => 1,
            'status' => array(0, 9),
            'channel' => 1,
        );

        $user_queue_info = APi::atom('punch/get_punch_syn_info', $params);

        if (empty($user_queue_info)) {
           echo '['.date('Y-m-d H:i:s').']'.' 无数据'."\r\n";
           exit;
        }

        foreach ($user_queue_info as $user_key => $user_content) {
            $user_value = json_decode($user_content['content'], true);
            $msg = '';
            //需要更改的数据的参数
            $update_params = array();
            switch ($user_value['status']) {
                //删除不能执行同步删除，否则，用户的考勤记录获取不到
                /*case 2: //删除
                    $return = $this->pushUserInfo($user_value, $this->delete_fields, 'delete');
                    $return['code'] = $user_value['user_id'];
                    $return['msg'] = "del: " ;
                    break;*/
                case 1: //修改和增加
                case 3:
                //case 2:
                    //修改和增加要去检测部门是否存在，不存在则 修改对为发送失败
                    if (!$this->getDepartInfo($user_value)) {//如果部门不存在
                        $return['success'] = false;
                    } else {
                        if ($this->getUserInfo($user_value)) {
                            $return = $this->pushUserInfo($user_value, $this->update_field, 'update');
                            $return['code'] = $user_value['user_id'];
                            $return['msg'] = "update " ;
                        } else {
                            $return = $this->pushUserInfo($user_value, $this->field, 'add');
                            $return['code'] = $user_value['user_id'];
                            $return['msg'] = "add " ;
                        }
                    }
                    break;
               
            }

            $update_params['id'] = array($user_content['id']);
            $update_params['send_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            if (isset($return['success']) && $return['success'] == true) {
                $update_params['status'] = 10;
            } else {
                $update_params['status'] = 9;
            }
            $msg = APi::atom('punch/update_punch_syn_info', $update_params);
            echo '['.date('Y-m-d H:i:s').']'.'更新的数据'.json_encode($update_params).';更新队列返回值:'.$msg.'发送数据返回值'.  json_encode($return)."\r\n";
            
        }
    }

    /**
     * 同步部门数据，增删该查
     *
     * @param array $param
     *
     * @return bool
     */
    protected function pushUserInfo($param = array(), $fields = array(), $method = "delete") {
        //return array('success' => true);
        //return array('success' => true);
        if(empty($param)) return array();
        $post_url = self::PUNCH_URL . '/api/employee';
        $data = $this->format_param($param, $fields, $method);
        
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
            'datatype' => 'employee',
            'optype' => $method,
            "key" => "mls-iwer13209ud8yr32oih928y43rt"
        );
        $tmp_params = array();
        foreach ($fields as $field_key => $field_value) {
            if($field_value == 'hire_time'){
                if($params['hire_time']=='0000-00-00'){
                    $tmp_params['hiredate']=date('Y-m-d',$_SERVER['REQUEST_TIME']);
                }else{
                    $tmp_params['hiredate']= $params['hire_time'];
                }
            }else if($field_value == 'no') {
                $tmp_params[$field_key] = '';
            }else{
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
    public function getUserInfo($params = array()) {
        if (empty($params)) {
            return false;
        }
        $code_array = array(
            'code' => isset($params['user_id']) ? $params['user_id'] : ''
        );
        $post_url = self::PUNCH_URL . '/api/employee';

        $this->url = $post_url . '?' . http_build_query($code_array);
        $curl_obj = new \Libs\Sphinx\curl;
        $ret = $curl_obj->get($this->url);
        $body = $ret['body'];
        $this->app->log->log('crontab/punch_push_user_info', $body);
        $body = json_decode($body, true);
        if (!empty($body['data'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询部门是否存在
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
        if (!empty($body['data'])) { //部门存在
            return true;
        } else {
            return false;
        }
    }

}
