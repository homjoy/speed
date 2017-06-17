<?php
namespace Admin\Modules\Hr\Leave;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Hr_leave\LeaveList;
use Admin\Package\Account\UserInfo;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  LeaveHome
 * Date: 15/11/25
 * Time: 下午12:18
 */
class LeaveHome extends BaseModule {
     protected $errors = NULL;
     private $params = NULL;
     private   $page_size = 20;
     private   $count = 1;
     protected $checkUserPermisssion = TRUE;
     public function run() {
         $this->_init();
         $queryParams=array();
         $queryParams['all'] =1;
         if(!empty($this->params['user_status'])){
             $queryParams['status']=$this->params['user_status'];
         }
         if(!empty($this->params['depart_id'])){
             $queryParams['depart_id']=$this->params['depart_id'];
         }
        //对user_id
         if(!empty($this->params['user_id'])){
             $queryParams['user_id']=$this->params['user_id'];
         }
         $temp_id= UserInfo::getInstance()->getUserInfo($queryParams);
         if(is_array($temp_id)){
             $this->params['user_id']  = array_keys($temp_id);
             $this->params['user_id'] =implode(',',$this->params['user_id']);
         }

         //分页控
         $this->params['offset']  = intval($this->params['page']-1)* $this->page_size;
         //count获取总条数
         $this->params=array_filter($this->params);

         if(empty($this->params['count'])){
             $this->params['count'] =$this->count;
         }
         $count = LeaveList::getInstance()->GetLeaveList($this->params);

         if ($count == FALSE) {//容错

             $return =  Response::gen_success(array());
             $return['count'] ='';
             $return['page'] ='';
             return $this->app->response->setBody($return);
         }
         unset( $this->params['count']);
         //获取当前20条数据

         $result = LeaveList::getInstance()->getLeaveList($this->params);
         $return = Response::gen_success($result);//返回总数
         $return['count'] = ceil($count/$this->page_size);
         $return['page'] = $this->params['page'];
         $this->app->response->setBody($return);


    }

    private function _init() {

        $this->rules = array(
            'absence_type'  => array(
                'type'    => 'integer',
                'default'=>'',
            ),
            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'limit'  => array(
                'type'    => 'integer',
                'default' => 20,
            ),
            'offset'  => array(
                'type'    => 'integer',
                'default' => 0,
            ),
            'end_time'  => array(
                'type'    => 'datetime',
                'default'=>'',
            ),
            'create_time'  => array(
                'type'    => 'datetime',
                'default'=>'',
            ),
            'user_status'  => array(
                'type'    => 'multiId',
                'default'=>'',
            ),
            'user_id'  => array(
                'type'    => 'multiId',
                'default'=>'',
            ),
            'sort'  => array(
                'type'    => 'string',
                'default'=>'DESC',
            ),
            'leave'  => array(
                'type'    => 'integer',
                'default'=>1,
            ),
            'depart_id'  => array(
                'type'    => 'integer',
            )

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}