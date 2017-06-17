<?php
namespace Admin\Modules\Worker;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Worker\Notify;
use Libs\Util\ArrayUtilities;
use Admin\Package\Account\UserInfo;
/**
 * Created by guojiezhu@meilishuo.com
 * User: MLS  ConfigHome
 * Date: 15/12/25
 * Time: 下午12:18
 */
class NotifyHome extends BaseModule
{
    protected $errors = null;
    private $params = null;
    private $page_size = 20;
    protected $notify_type = array(
        1 =>'邮件',
        2 =>'短信',
        4 => 'im',

    );
    protected $status_type = array(
        0 => '未发送',
        1 => '发送中',
        2 => '发送取消',
        3 => '消息丢弃',
        9 => '发送失败',
        10 => '发送成功',
        4=>'敏感词',
        5 =>'黑名单',

    );
    protected $checkUserPermisssion = TRUE;
    public function run()
    {
        $this->_init();
        //page  count  notify_type   data search_params
        if(empty($this->params['to_id'])){
            unset($this->params['to_id']);
        }
        $this->params['offset']  = intval($this->params['page']-1)* $this->page_size;
        $this->params['limit']  =  $this->page_size;
        $data =  Notify::getInstance()->getNotify($this->params);
      
        //只给前端输出想要的字段
        $end_data= $temp =array();
        if(!empty($data)&&is_array($data)){
            foreach($data as $key=>$value){
                $temp['notify_id'] = isset($value['notify_id'])?$value['notify_id']:'';
                $temp['to_id'] = isset($value['to_id'])?$value['to_id']:'';
                $temp['channel'] = isset($this->notify_type[$value['channel']])?$this->notify_type[$value['channel']]:'';
                $temp['content'] = isset($value['content'])? $value['content']:'';
                $temp_content = json_decode($temp['content'],true);
                if(is_array($temp_content)) {
                    $keys = array_keys($temp_content);
                    $temp_content_info = current($temp_content);
                    $sysmbol_ley = current($keys);
                    $temp_content_string = $this->deep_foreach($temp_content_info, $sysmbol_ley);
                }else{
                    $temp_content_string = $temp['content'];
                }
                $temp['temp_content'] = $temp_content_string;
                $temp['after_content'] = $value['after_content'];
                $temp['title'] = isset($value['title'])?$value['title']:'';
                $temp['send_at'] = isset($value['send_at'])?$value['send_at']:'';
                $temp['update_time'] = isset($value['update_time'])?$value['update_time']:'';
                $temp['weights'] = isset($value['weights'])?$value['weights']:100;
                $temp['send_times'] = isset($value['send_times'])?$value['send_times']:0;
                if(isset($value['status'])){
                    $temp['status'] =  isset($this->status_type[$value['status']])?$this->status_type[$value['status']]:'';
                }
                $end_data[] =   $temp;
               
                
            }
        }
        $return =  Response::gen_success($end_data);
        $this->params['count'] =1;
        $count =  Notify::getInstance()->getNotify($this->params);

        if($count){
            $return['count'] = ceil($count/$this->page_size);
        }else{
            $return['count']='';
        }
        $return['page'] = $this->params['page'];
        if(is_array($this->params['channel'])&&count($this->params['channel'])==1){
            $this->params['channel'] =array_pop($this->params['channel']);
        }
        if(!empty( $this->params ['to_id'])) {
            $user_info = UserInfo::getInstance()->getUserInfo(array('user_id' => $this->params ['to_id']));
            $current_user_info = current($user_info);
            $return['search_user_info'][] = array('name' => $current_user_info['name_cn'],'user_id' =>$current_user_info['user_id'] );

        }else{
            $return['search_user_info'][] = array();
        }
        $return['search_params'] = $this->params;
        $return['notify_type'] =$this->notify_type;
        $this->app->response->setBody($return);

    }
    
    function deep_foreach ($arr, $k='', $pre_indent = '') {
        $return_str  = '';
        if (!is_array ($arr)) {
          return $return_str;
        }

        $str = $k ? "[$k] => " : '';
        $cur_indent = $pre_indent . " ";

        $return_str .= $pre_indent.$str."Array$pre_indent(<br/>";

        foreach ($arr as $key => $val ) {
          if (is_array ($val)) {
            $return_str .=$this->deep_foreach ($val, $key, $cur_indent);
          } else {
            $return_str .= $cur_indent."[$key] = > ".$val.'<br/>';
          }
        }

        $return_str .= $pre_indent.")<br/>";
        return $return_str;
    }

    private function _init()
    {//path  father_id

        $this->rules = array(
            'page' => array(
                'type'    => 'integer',
                'default' => 1
            ),
            'to_id' => array(
                'type'    => 'integer',
            ),
            'channel'=> array(
                'type'=>'multiId',
                'default'=>array(1,2,4),
            ),
            'title'=> array(
                'type'=>'string',

            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}