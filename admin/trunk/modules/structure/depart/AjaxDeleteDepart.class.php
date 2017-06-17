<?php
namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
use Admin\Package\Account\UserInfo;
use Admin\Package\Department\DepartSub;
use Admin\Package\Department\DepartRelation;
use Admin\Package\Log\Log;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS  AjaxDeleteDepart 暂不开放
 * Date: 15/9/25
 * Time: 下午12:18
 */
class AjaxDeleteDepart extends BaseModule {

    private $errors = NULL;
    private $params = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;
    private static $DEPART_TYPE = 5;
    public function run() {
     return  $this->app->response->setBody(Response::gen_error(50001,'删除功能目前不开放，请通知管理员处理'));
     $this->_init();
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }
    $old = Department::getInstance()->getDepartTemp(array('depart_id'=>$this->params['depart_id']));
    //查看是否有这个部门
    $is_depart = $this->existDepart($this->params);
    if(empty($is_depart)){
        return $this->app->response->setBody(Response::gen_error(30001,'亲,没有这个部门'));
    }
    //查看部门下面是否有人
     $is_user = $this->existUser($this->params);
     if(!empty($is_user)){
            return $this->app->response->setBody(Response::gen_error(30001,'亲,这个部门里面有人，要删了他们不知道该去哪了'));
     }
     $is_child = $this->existChildren($this->params['depart_id']);
      //查看部门下面是否有子部门
     if(empty($is_child)){
            return $this->app->response->setBody(Response::gen_error(30001,'亲,这个部门下面还有部门，不能删啦'));
     }
    //部门表状态修改
     $depart_result = Department::getInstance()->updateDepartTemp($this->params);

    if(empty($depart_result)){
        return $this->app->response->setBody(Response::gen_error(50001,'亲,删除部门失败了'));
    }
    //部门relation_id  获取
    $temp = DepartRelation::getInstance()->getRelationTempInfo( array('is_virtual'=>0,
            'depart_id'=>$this->params['depart_id']));
     if(is_array($temp)){
         $temp = array_pop($temp);
         $temp = isset($temp['relation_id'])?$temp['relation_id']:'';
     }
     //关系表状态修改
     $relation_result = DepartRelation::getInstance()->updateRelationTempInfo(array('status'=>0,
            'relation_id'=>$temp));
     if($relation_result ===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'亲,删除关系失败了'));
      }
     //sub表状态
     $sub_result =DepartSub::getInstance()->updateSubTempInfo( array('status'=>0,
            'relation_id'=>$temp));
     if($sub_result ===FALSE){
            return $this->app->response->setBody(Response::gen_error(50001,'亲,删除代理失败了'));
     }
      $this->doLog($this->params);
      $this->app->response->setBody(Response::gen_success('删除部门成功'));

    }
    private function existDepart($params){
        $depart_info = Department::getInstance()->getDepartTemp( $params);

        if(is_array($depart_info)){
             $depart_info =array_pop($depart_info);
        }
        return $depart_info;
    }
    private function existChildren($depart_id){
        $depart_info = Department::getInstance()->getDepartTemp( array('status'=>1,'all'=>1));

        if(is_array($depart_info)){
           foreach($depart_info as $k => $v){
               if(isset($v['parent_id'])){
                   if($v['parent_id'] ==$depart_id ){
                       return FALSE;
                   }
               }
           }
           return TRUE;
         }
        return FALSE;
    }
    private function existUser($params){
        $user_info = UserInfo::getInstance()->getUserInfo( $params);

        if(is_array($user_info)){
            $user_info =array_pop($user_info);
        }
        return $user_info;
    }
    protected function doLog($new_param=array(),$old_param='delete'){

        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],
                'handle_id'=>isset($new_param['depart_id'])?$new_param['depart_id']:'',
                'before_data'=>$old_param,
                'after_data'=>json_encode($new_param),
                'handle_type'=>self::$DEPART_TYPE
            )
        );
        return $ret;
    }

    private function _init() {

        $this->rules = array(
            'depart_id' => array(
                'required'   => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
            'status' => array(
                'type'=>'integer',
                'default'=>0,
            ),

        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}