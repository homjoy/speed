<?php
namespace Atom\Modules\Account;

use Atom\Package\Account\DepartmentSub;
use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentInfo;
use Libs\Util\ArrayUtilities;
use Atom\Package\Account\UserAvatar;
use Atom\Package\Migrate\Crab;
/**
 * 获取直属下级
 * @author haibinzhou@meilishuo.com
 * @since 2015-08-25
 */

class GetLowerLevelUser extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $queryParams;
    protected $errors = array();

    public function run() {

        $this->_init();

        if(!empty($this->params['user_id']) && $this->params['user_id'] >0 ){
            $queryParams['user_id'] = $this->params['user_id'];
        }else if(isset($this->params['mail'])  && !empty($this->params['mail'])){
            $queryParams['mail'] = $this->params['mail'];
        }else{
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        //新库
        $result = $this->getLowerUser($queryParams);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success(Format::outputData($result));
        }

        $this->app->response->setBody($return);
    }

    private function getLowerUser($params){
        $user_info = UserInfo::model()->getDataList($params);
        if(empty($user_info)){
            return FALSE;
        }

        $user_info = array_pop($user_info);
        $user['user_id'] = $user_info['user_id'];
       // $user['is_virtual'] = array(0,1);
        $relation = DepartmentRelation::model()->getDataList($user);
        $sub      = DepartmentSub::model()->getDataList($user);
        $leader = array_merge($relation,$sub);
        if(empty($leader)){  //如果不是领导
            return $leader;
        }

        $result = array();
        $relationid = ArrayUtilities::my_array_column($relation,'relation_id'); //获取当前领导所管理的部门id
        $sub = DepartmentSub::model()->getDataList($user);
        $subRelationid = ArrayUtilities::my_array_column($sub,'relation_id');
        //获取当前管理部门
        $relation_id['relation_id'] = array_merge($relationid,$subRelationid);
        $depart = DepartmentRelation::model()->getDataList($relation_id);
        $depart_id = array_unique(ArrayUtilities::my_array_column($depart,'depart_id'));
        $depart_name['depart_id'] = $depart_id;
        $result = $this->userInfos($depart_name,$user['user_id']);


        $users['user_id']    = $user_info['user_id'];
        $users['is_virtual'] = array(0,1);
        $relation_virtual = DepartmentRelation::model()->getDataList($users);
        $sub_virtual      = DepartmentSub::model()->getDataList($user);
        $merge_virtual = array_merge($relation_virtual,$sub_virtual);

        $parent_relation['parent_relation_id'] = ArrayUtilities::my_array_column($merge_virtual,'relation_id');
        $parent_relation['is_virtual']         = array(0,1);
        $relation_lowers = DepartmentRelation::model()->getDataList($parent_relation);

        $lowerLeader = array();
        foreach($relation_lowers as $val){
            if($val['user_id'] == 0){   //如果下级领导离职
                $sub['relation_id'][] = $val['relation_id'];
            }else{
                $lowerLeader['user_id'][] = $val['user_id'];
            }
        }
        if(!empty($sub)){ //如果有代替领导
            $sub_info = DepartmentSub::model()->getDataList($sub);
            $sub_info = ArrayUtilities::my_array_column($sub_info,'user_id'); //获取代理领导的uid
            if(isset($lowerLeader['user_id'])){
                $lowerLeader['user_id'] = array_merge($lowerLeader['user_id'],$sub_info);
            }else{
                $lowerLeader['user_id'] = $sub_info;
            }
        }

        //合并直属员工和直属领导
        if(!empty($lowerLeader)){
            $lowerLeader['user_id'] = array_unique($lowerLeader['user_id']);
            $u_infos = $this->userInfos($lowerLeader,$user['user_id']);
            $result = array_merge($result,$u_infos);
        }

        $result = $this->drop_repeat($result,'user_id');

        return $result;
    }

    private function userInfos($user = array(),$userid){
        $user_infos = UserInfo::model()->getDataList($user,0,9999); //获取各级部门
        $user_avatar = UserAvatar::model()->getDataList($user,0,9999);
        $user_avatar = ArrayUtilities::hashByKey($user_avatar,'user_id');

        if(!empty($user_avatar)){
            foreach($user_infos as &$value){
                if(isset($user_avatar[$value['user_id']])){
                    $value['avatar_big'] = BEANSDB_DOMAIN.$user_avatar[$value['user_id']]['avatar_big'];
                    $value['avatar_src'] = BEANSDB_DOMAIN.$user_avatar[$value['user_id']]['avatar_src'];
                    $value['avatar_middle'] = BEANSDB_DOMAIN.$user_avatar[$value['user_id']]['avatar_middle'];
                    $value['avatar_small'] = BEANSDB_DOMAIN.$user_avatar[$value['user_id']]['avatar_small'];
                }
                if(!isset($value['avatar_big'])){
                    $value['avatar_big'] = DEFAULT_PHOTO;
                    $value['avatar_src'] = DEFAULT_PHOTO;
                    $value['avatar_middle'] = DEFAULT_PHOTO;
                    $value['avatar_small'] = DEFAULT_PHOTO;
                }
                $value['mail'] = $value['mail'].'@meilishuo.com';
                $value['direct_leader'] = $userid;
            }
        }

        $depart_infos['depart_id'] = ArrayUtilities::my_array_column($user_infos,'depart_id');
        $depart_infos['depart_id'] = array_unique($depart_infos['depart_id']);
        $depart_data = DepartmentInfo::model()->getDataList($depart_infos);
        foreach($user_infos as $k=>$infos){
            foreach($depart_data as $data){
                if($infos['depart_id'] == $data['depart_id']){
                    $user_infos[$k]['depart'] = $data['depart_name'];
                }
                if($k == $userid){  //把领导自己删掉
                    unset($user_infos[$k]);
                }
            }
        }

        return $user_infos;
    }

    //按照某个字段去除重复
    private  function drop_repeat($data,$id){
        $arr_id = array();
        $arr_data = array();
        foreach($data as $key=>$value){
            if($key==0){
                $arr_id[] = $value[$id];
                $arr_data[] = $value;
            }else{
                if(!in_array($value[$id],$arr_id)){
                    $arr_id[] = $value[$id];
                    $arr_data[] = $value;
                }
            }

        }
        return $arr_data;
    }
    /**
     * 参数初始化
     */
    protected function _init()
    {

        $this->rules = array(
            'user_id'=>array(
                'type'=>'integer',
            ),
            'mail' => array(
                'type'=>'string',
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
