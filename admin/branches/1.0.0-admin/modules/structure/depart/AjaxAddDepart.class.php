<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
use Admin\Package\Department\DepartRelation;
use Admin\Package\Department\DepartSub;
use Admin\Package\Account\UserInfo;
use Admin\Package\Log\Log;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxAddDepart
 * Date: 15/9/25
 * Time: 下午12:18
 */
class AjaxAddDepart extends BaseModule {
    protected $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    public static $ROOT_DEPART=0;
    public static $CEO_DEPART=1;
    public static $ROOT_RELATION=0;
    public static $ROOT_DEPART_LEVEL=0;
    //log
    private static $DEPART_TYPE = 5;
    private static $USER_TYPE = 4;
    private static $SUB_TYPE = 9;
    private static $RELATION_TYPE = 10;
    public function run() {

       $this->_init();

       if( $this->post()->hasError()){
           $return = Response::gen_error(10001, '', $this->errors);
           return $this->app->response->setBody($return);
       }
        if(isset($this->params['depart_name'])){
            $this->params['depart_name'] =htmlspecialchars_decode(  $this->params['depart_name']);
        }
        if(isset($this->params['depart_info'])){
            $this->params['depart_info'] =htmlspecialchars_decode(  $this->params['depart_info']);
        }
       $depart_info= $relation_info= $sub_info = array();
       if(empty($this->params['leader_id']) && empty($this->params['sub_leader_id'])){
           return $this->app->response->setBody(Response::gen_error(10001,'领导必须要有'));
       }
       if(!empty($this->params['virtual_leader_id']) && empty($this->params['virtual_role_id'])){
            return $this->app->response->setBody(Response::gen_error(10001,'汇报关系角色和领导必须同时存'));
       }

       //级别的单独处理不让前台修改
       if(!empty($this->params['parent_id'])){
           $depart_info = Department::getInstance()->getDepartTemp(
               array(
                   'depart_id'=>$this->params['parent_id']
               )
           );
           $depart_info = is_array($depart_info)?array_pop($depart_info):'';
           $depart_info = isset($depart_info['depart_level'])?$depart_info['depart_level']:'';
           $this->params['depart_level'] = intval($depart_info)+1;
       }elseif($this->params['parent_id']===self::$ROOT_DEPART){
           $this->params['depart_level'] = 1;
       }

       $depart_info = Department::getInstance()->createDepartTemp($this->params);
       $query_params = $this->params;
       $query_params['depart_id'] =$depart_info;
       $this->doLog($query_params,'add',self::$DEPART_TYPE);

       //log
       $this->params['depart_id'] =$depart_info;
       //批量添加成员
       if(isset($this->params['user_id'])&&is_array($this->params['user_id'])){

           $this->params['user_id']= array_unique($this->params['user_id']);
           foreach($this->params['user_id'] as $key => $value){
               $add_user = UserInfo::getInstance()->updateUserInfo(
                   array(
                       'user_id'=>$value,
                       'depart_id'=> $depart_info
                   )
               );
               if($add_user === FALSE){
                   return $this->app->response->setBody(Response::gen_error(50001,'添加部门人员失败'));
               }
              $this->doLog(array('user_id'=>$value
               ,'depart_id'=> $depart_info),'add',self::$USER_TYPE);
           }
       }
      //添加relation
      if($this->params['parent_id']===self::$ROOT_DEPART){
          $this->params['parent_id'] =self::$CEO_DEPART;
      }
      //得到父亲的relation
      $parent = DepartRelation::getInstance()->getRelationTempInfo(
            array(
                'depart_id'=> $this->params['parent_id'],
                'is_virtual'=>0
            )
      );
      $parent =is_array($parent)?array_pop($parent):'';
      if(isset($parent['relation_id'])){
          //根据role user_id  父亲的relation 生成新的关系
          $relation_info = DepartRelation::getInstance()->createRelationTempInfo(
              array(
                  'role_id'=>$this->params['role_id'],
                  'user_id'=>$this->params['leader_id'],
                  'depart_id'=>$this->params['depart_id'],
                  'parent_relation_id'=>$parent['relation_id']
              )
          );

        $query_params['relation_id'] =$relation_info;
        $this->doLog($query_params,'add',self::$RELATION_TYPE);
      }else{

          return $this->app->response->setBody(Response::gen_error(50001,'创建关系失败'));
      }
      //添加上级汇报关系
      if(!empty($this->params['virtual_leader_id'])){
          //那倒上一步骤的$relation_info
           $depart  =  UserInfo::getInstance()->getUserInfo(
                    array(
                        'user_id'=>$this->params['virtual_leader_id']
                    )
                );
          $depart = is_array($depart)?array_pop($depart):'';
          $depart = isset($depart['depart_id'])?$depart['depart_id']:'';

          $p_r = DepartRelation::getInstance()->createRelationTempInfo(
              array(
                  'role_id'=>$this->params['virtual_role_id'],
                  'user_id'=>$this->params['virtual_leader_id'],
                  'depart_id'=>$depart,
                  'parent_relation_id'=>$parent['relation_id'],
                  'is_virtual'=>1
              )
          );
          if($p_r===FALSE){
              return $this->app->response->setBody(Response::gen_error(50001,'创建汇报关系失败'));
          }
         $this->doLog(    array(
                  'role_id'=>$this->params['virtual_role_id'],
                  'user_id'=>$this->params['virtual_leader_id'],
                  'depart_id'=>$depart,
                  'parent_relation_id'=>$parent['relation_id'],
                  'is_virtual'=>1,
                  'relation_id'=>$p_r
              ),'add',self::$RELATION_TYPE);

          $u_r = DepartRelation::getInstance()->updateRelationTempInfo(
              array(
                  'relation_id'=>$relation_info,
                  'parent_relation_id'=>$p_r,
              )
          );
          if($u_r===FALSE){
              return $this->app->response->setBody(Response::gen_error(50001,'修改部门关系失败'));
          }
          $this->doLog(  array(
                  'relation_id'=>$relation_info,
                  'parent_relation_id'=>$p_r,
              ),'update',self::$RELATION_TYPE);

      }

       //是否需要添加sub
       if(!empty($this->params['sub_leader_id'])){
           $sub_info['user_id'] =  $this->params['sub_leader_id'];
           $sub_info['relation_id'] = $relation_info;
           $query_params =$sub_info;
           $sub_info = DepartSub::getInstance()->createSubTempInfo($sub_info);
           if($sub_info===FALSE){
               return $this->app->response->setBody(Response::gen_error(50001,'创建替补领导失败'));
           }
          $query_params['sub_id'] =$sub_info;
          $this->doLog($query_params,'add',self::$SUB_TYPE);
       }
       
       $this->app->response->setBody(Response::gen_success('创建成功'));
    }
    protected function doLog($new_param=array(),$old_param='update',$status){
        $type =array();
        switch($status){
            case 5:
               $type = isset($new_param['depart_id'])?$new_param['depart_id']:'';
                break;
            case 4:
                $type = isset($new_param['user_id'])?$new_param['user_id']:'';
                break;
            case 9:
                $type = isset($new_param['sub_id'])?$new_param['sub_id']:'';
                break;
            case 10:
                $type = isset($new_param['relation_id'])?$new_param['relation_id']:'';
                break;
        }

        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],
                'handle_id'=>$type,
                'operation_type'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>$status
            )
        );
        return $ret;
    }

    private function _init() {

       $this->rules = array(

           'depart_name' => array(
               'required' => TRUE,
               'allowEmpty' => FALSE,
               'type'=>'string',
               'maxLength'=> 30,
           ),
           'leader_id' => array(//leader_id
               'type'=>'integer',
               'maxLength'=> 30,
           ),
           'sub_leader_id' => array(//代理leader_id
               'type'=>'integer',
               'maxLength'=> 30,
           ),
           'virtual_leader_id' => array(//虚拟部门leader_id
               'type'=>'integer',
               'maxLength'=> 30,
           ),
           'depart_info' => array(
               'type'=>'string',
               'maxLength'=> 300,
           ),
            'role_id' => array(// 角色
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
           'parent_id' => array(
               'required' => TRUE,
               'allowEmpty' => FALSE,
               'type'=>'integer',
           ),
           'child_id' => array(
               'type'=>'string',
               'maxLength'=> 30,
           ),
           'memo' => array(
               'type'=>'string',
               'maxLength'=> 300,
           ),

           'status' => array(
               'type'=>'integer',
               'enum'=> array(0,1),
               'default' => 1,
           ),
           'user_id' => array(
               'type'=>'multiID',
               'default'=>array(),
           ),
          'virtual_role_id' => array(// 角色
                'type'=>'integer',
            ),
       );
       $this->params = $this->post()->safe();
       $this->errors = $this->post()->getErrors();

   }
}