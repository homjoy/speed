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
 * User: MLS
 * Date: 15/9/25
 * Time: 下午9:52
 */

class AjaxUpdateDepart extends BaseModule {//改良版

    protected $errors = NULL;
    private $params = NULL;

    public  static $VIEW_SWITCH_JSON = TRUE;
    private static $ROOT_DEPART=0;
    private static $CEO_DEPART=1;
    private static $ROOT_DEPART_LEVEL=0;
    private static $ROOT_RELATION=0;

    private static $DEPART_TYPE = 5;
    private static $USER_TYPE = 4;
    private static $SUB_TYPE = 9;
    private static $RELATION_TYPE = 10;
    public function run() {

       $this->_init();
       if($this->post()->hasError()){
           $return = Response::gen_error(10001, '', $this->post()->getErrors());
           return $this->app->response->setBody($return);
       }
        //格式化
        if(isset($this->params['depart_name'])){
            $this->params['depart_name'] =htmlspecialchars_decode(  $this->params['depart_name']);
        }
        if(isset($this->params['depart_info'])){
            $this->params['depart_info'] =htmlspecialchars_decode(  $this->params['depart_info']);
        }
        //校验
       $depart_info= $relation_info= $sub_info = array();
       if(empty($this->params['leader_id'])&& empty($this->params['sub_leader_id'])){
           return $this->app->response->setBody(Response::gen_error(10001,'领导必须要有'));
       }
        if(!empty($this->params['virtual_leader_id']) && empty($this->params['virtual_role_id'])){
            return $this->app->response->setBody(Response::gen_error(10001,'汇报关系角色和领导必须同时存'));
        }
       //对不需要维护数据判断
       $ret =DepartRelation::getInstance()->getRelationTempInfo(
           array(
               'depart_id'=>$this->params['depart_id'],
               'is_virtual'=>0
           )
       );
       if(!$ret){
           return $this->app->response->setBody(Response::gen_error(10004,'此部门不要维护'));
       }
       //部门人员变动
       if(is_array($this->params['user_id'])&& isset($this->params['depart_id'])){
           $this->params['user_id']= array_unique($this->params['user_id']);
           foreach($this->params['user_id'] as $key => $value){
               $add_user = UserInfo::getInstance()->updateUserInfo(
                   array(
                       'user_id'=>$value,
                       'depart_id'=> $this->params['depart_id']
                   )
               );
              if($add_user === FALSE){
                  return $this->app->response->setBody(Response::gen_error(50001,'添加部门人员失败'));
               }
              $this->doLog(  array(
                   'user_id'=>$value,
                   'depart_id'=> $this->params['depart_id']
               ),'update',self::$USER_TYPE);
           }
       }
        //修改level
        //修改部门信息
        if($this->updateDepart($this->params)===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'部门信息更新失败'));
        }

        //leader
        if($this->updateLeader($this->params)===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'部门领导更新失败'));
        }

        //sub
        if($this->updateSub($this->params)===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'代理领导更新失败'));
        }
        if($this->params['depart_id']==self::$CEO_DEPART){
            return $this->app->response->setBody(Response::gen_success('成功'));
        }
        //relation
        if($this->updateRelation($this->params)===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'汇报关系更新失败'));
        }

        $this->app->response->setBody(Response::gen_success('成功'));
    }
    private function updateRelation($params=array()){
        $son = DepartRelation::getInstance()->getRelationTempInfo(
            array(
                'depart_id'=>$this->params['depart_id'],
                'is_virtual'=>0,
            )
        );
        $son = is_array($son)?array_pop($son):'';
        if( !isset($son['relation_id'])){
            return FALSE;
        }

        $parent = DepartRelation::getInstance()->getRelationTempInfo(
            array(
                'relation_id'=>$son['parent_relation_id'],
                'is_virtual'=>array(1,0),
            )
        );
        $parent = is_array($parent)?array_pop($parent):'';// 存在特殊情况 1

         if(empty($this->params['virtual_leader_id'])){//现在没有
             //原来有
             if(isset($parent['is_virtual'])&& $parent['is_virtual']==1){
                 //删除原来的
                 $delete = DepartRelation::getInstance()->updateRelationTempInfo(
                     array(
                         'relation_id'=>$son['parent_relation_id'],
                         'status'=>0,
                     )
                 );
                 if($delete===FALSE){
    
                     return FALSE;
                 }
                $this->doLog(   
                  array(
                         'relation_id'=>$son['parent_relation_id'],
                         'status'=>0,
                  ),'delete',self::$RELATION_TYPE);
               
             }
              $temp =array();
               if($params['parent_id']==self::$ROOT_DEPART){
                   $temp =self::$CEO_DEPART;
               }else{
                   //拿到真正父亲的relation
                   $true_parent = DepartRelation::getInstance()->getRelationTempInfo(
                       array(
                           'depart_id'=>$params['parent_id'],
                           'is_virtual'=>0,
                       )
                   );
                   $true_parent = is_array($true_parent)?array_pop($true_parent):'';
                   $temp  =isset($true_parent['relation_id'])?$true_parent['relation_id']:'';

               }
               //更新回来
               $update = DepartRelation::getInstance()->updateRelationTempInfo(
                   array(
                       'relation_id'=>$son['relation_id'],
                       'parent_relation_id'=>$temp,
                   )
               );
               if($update===FALSE){
                   return FALSE;
               }
              $this->doLog(   
                 array(
                       'relation_id'=>$son['relation_id'],
                       'parent_relation_id'=>$temp,
                   ),'update',self::$RELATION_TYPE);
             return TRUE;
         }else{//现在有

             if(isset($parent['is_virtual'])&& $parent['is_virtual']==1){
                 //原来有
                   //干掉原来的线
                 $delete = DepartRelation::getInstance()->updateRelationTempInfo(
                     array(
                         'relation_id'=>$son['parent_relation_id'],
                         'status'=>0,
                     )
                 );
                 if($delete===FALSE){
                 
                     return FALSE;
                 }
                 $this->doLog(   
                    array(
                         'relation_id'=>$son['parent_relation_id'],
                         'status'=>0,
                     ),'delete',self::$RELATION_TYPE);
             }
             //获取parent_relation_id
             $temp =array();
             if($params['parent_id']==self::$ROOT_DEPART){
                 $temp =self::$CEO_DEPART;
             }else{
                 //拿到真正父亲的relation
                 $true_parent = DepartRelation::getInstance()->getRelationTempInfo(
                     array(
                         'depart_id'=>$params['parent_id'],
                         'is_virtual'=>0,
                     )
                 );
                 $true_parent = is_array($true_parent)?array_pop($true_parent):'';
                 $temp  =isset($true_parent['relation_id'])?$true_parent['relation_id']:'';

             }
             //获取depart_id
             $depart =UserInfo::getInstance()->getUserInfo(
                 array(
                     'user_id'=>$params['virtual_leader_id'],
                 )
             );
             $depart = is_array($depart)?array_pop($depart):'';
             $depart =isset($depart['depart_id'])?$depart['depart_id']:'';
             //创建新的线
             $create =DepartRelation::getInstance()->createRelationTempInfo(
                 array(
                     'role_id'=>$params['virtual_role_id'],
                     'user_id'=>$params['virtual_leader_id'],
                     'depart_id'=>$depart,
                     'parent_relation_id'=>$temp,
                     'is_virtual'=>1
                 )
             );
             if($create===FALSE){

                 return FALSE;
             }
            $this->doLog(   
                   array(
                     'role_id'=>$params['virtual_role_id'],
                     'user_id'=>$params['virtual_leader_id'],
                     'depart_id'=>$depart,
                     'parent_relation_id'=>$temp,
                     'is_virtual'=>1,
                     'relation_id'=>$create
                 ),'add',self::$RELATION_TYPE);
             //更新部门关系
             $update = DepartRelation::getInstance()->updateRelationTempInfo(
                 array(
                     'relation_id'=>$son['relation_id'],
                     'parent_relation_id'=>$create,
                 )
             );
             if($update===FALSE){
       
                 return FALSE;
             }
             $this->doLog(   
                    array(
                     'relation_id'=>$son['relation_id'],
                     'parent_relation_id'=>$create,
                 ),'update',self::$RELATION_TYPE);
             return TRUE;
         }
    }
   private function updateLeader($params=array()){
       if(isset($params['depart_id'])&&isset($params['leader_id'])){
           //获取当前部门的真实relation
          $p = DepartRelation::getInstance()->getRelationTempInfo(
               array(
                   'depart_id'=>$params['depart_id'],
                   'is_virtual'=>0
               )
          );
           $p = is_array($p)?array_pop($p):'';
           $p = isset($p['relation_id'])?$p['relation_id']:'';
           //更新relation中领导
           $p = DepartRelation::getInstance()->updateRelationTempInfo(
               array(
                   'relation_id'=>$p,
                   'user_id'=>$params['leader_id'],
                   'role_id'=>$params['role_id']
               )
           );
           //可能出现一个人管理两个部门 所以没更新也是对的
           if($p!==FALSE){
             $this->doLog(   
                    array(
                   'relation_id'=>$p,
                   'user_id'=>$params['leader_id'],
                   'role_id'=>$params['role_id']
               ),'update',self::$RELATION_TYPE);
               return TRUE;
           }

       }
        return FALSE;
    }

   private function updateSub($params=array()){

        if(isset($params['depart_id'])&&isset($params['sub_leader_id'])){
            //获取relation在到sub中拿到相应的relation更新或者创建
            $p = DepartRelation::getInstance()->getRelationTempInfo(
                array(
                    'depart_id'=>$params['depart_id'],
                    'is_virtual'=>0
                )
            );
            $p = is_array($p)?array_pop($p):'';
            $p = isset($p['relation_id'])?$p['relation_id']:'';

            $get_sub=DepartSub::getInstance()->getSubTempInfo(
                array(
                    'relation_id'=>$p
                )
            );

            if($get_sub){
                //原有代理

                if(!empty($params['sub_leader_id'])){
                    $p = DepartSub::getInstance()->updateSubTempInfo(
                        array(
                            'relation_id'=>$p,
                            'user_id'=>$params['sub_leader_id']
                        )
                    );
                }else{
                    $p = DepartSub::getInstance()->updateSubTempInfo(
                        array(
                            'relation_id'=>$p,
                            'status'=>0,
                        )
                    );
                }


            }elseif($get_sub===FALSE){//请求接口出错
                return FALSE;
            }else{
                //原无代理 要添加代理
                if(!empty($params['sub_leader_id'])){
                    $p = DepartSub::getInstance()->createSubTempInfo(
                        array(
                            'relation_id'=>$p,
                            'user_id'=>$params['sub_leader_id'],
                        )
                    );
                }else{
                    $p = TRUE;
                }
            }
            if($p!==FALSE){
                  $this->doLog($params,'update',self::$SUB_TYPE);
                  return TRUE;
            }
        }
        return FALSE;
    }

   private function updateDepart($params=array()){
        //旧数据拿到 parent_id
        $result =  Department::getInstance()->getDepartTemp(
            array(
                'depart_id'=>$params['depart_id']
            )
        );
        $result = is_array($result)?array_pop($result):'';
       //更新部门信息
        $up_d = Department::getInstance()->updateDepartTemp($params);
        if($up_d === FALSE){
            $this->doLog($params,__LINE__.'修改部门信息失败');
            return FALSE;
        }
        //父亲id不相等时才改变level
        if(isset($result['parent_id']) &&($result['parent_id']!=$params['parent_id'])){
            //level
            $parent_info = $update_depart = $son =array();
            $parent_info = Department::getInstance()->getDepartTemp(
                array(
                    'depart_id'=>$params['parent_id']
                )
            );
            if($parent_info===FALSE){//接口没通
                return FALSE;
            }
            //拿到当前父亲部门的level
            $parent_info=   is_array($parent_info)?array_pop($parent_info):'';
            if(empty($parent_info)){
                $parent_info['depart_level']=self::$ROOT_DEPART_LEVEL;
            }
            //修改部门level
            if(isset($parent_info['depart_level'])){
                $update_depart  =  Department::getInstance()->updateDepartTemp(
                array(
                 'depart_id'=>$params['depart_id']
                ,'depart_level'=>$parent_info['depart_level']+1
                ,'parent_id'=>$params['parent_id']));

                if($update_depart===FALSE){
                    $this->doLog($params,__LINE__.'部门level修改失败');
                    return FALSE;
                }elseif($update_depart){
                    //level的递归改变子部门
                    $son = $this->updateLevel(
                        array(
                            'depart_id'=>$params['depart_id'],
                            'depart_level'=>$parent_info['depart_level']+1
                        )
                    );
                    if($son===FALSE){
                        $this->doLog($params,__LINE__.'子部门level修改失败');
                        return FALSE;
                    }
                }
            }

         }
    }

   private function updateLevel($parent=array()){//level  depart_id
       $parent_temp =$parent;
       if(!isset($parent_temp['depart_id'])){
           return FALSE;
       }
       $child = Department::getInstance()->getDepartTemp(
           array(
               'parent_id'=>$parent_temp['depart_id']
           )
       );

       if(empty($child)|| !is_array($child)){
           return TRUE;
       }elseif(is_array($child)){
           foreach($child as $k =>$v){
               $ret  =  Department::getInstance()->updateDepartTemp(
                   array(
                       'depart_id'=>$v['depart_id'],
                       'depart_level'=>$parent_temp['depart_level']+1
                   )
               );
               if(!empty($ret)){//这里判断子level
                    $this->updateLevel(
                        array(
                            'depart_id'=>$v['depart_id'],
                            'depart_level'=>$parent_temp['depart_level']+1)
                    );
               }
           }
       }

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

       $this->rules  = array(
           'depart_id' => array(
               'required' => TRUE,
               'allowEmpty' => FALSE,
               'type'=>'string',
               'maxLength'=> 30,
           ),
           'depart_name' => array(
               'type'=>'string',
               'maxLength'=> 30,
           ),
           'leader_id' => array(//leader_id
               'type'=>'integer',
           ),
           'sub_leader_id' => array(//代理leader_id
               'type'=>'integer',
               'maxLength'=> 30,
           ),
           'virtual_leader_id' => array(//虚拟部门leader_id
               'type'=>'integer',
               'maxLength'=> 30,
           ),
           'user_id' => array(//部门成员
               'type'=>'multiID',
               'default'=>array(),
           ),
           'depart_info' => array(
               'type'=>'string',
               'maxLength'=> 300,
           ),
            'role_id' => array(
                'type'=>'integer',
                'required' => TRUE,
                'allowEmpty' => FALSE,
            ),
           'parent_id' => array(
               'required' => TRUE,
               'allowEmpty' => FALSE,
               'type'=>'integer',
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
            'virtual_role_id' => array(
               'type'=>'integer',
           ),

       );

       $this->params   = $this->post()->safe();
       $this->errors   = $this->post()->getErrors();


   }
}
