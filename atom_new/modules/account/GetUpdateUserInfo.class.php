<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserLogin;
use Atom\Package\Account\UserInfo;
use Atom\Package\Account\DepartmentInfo;
use Libs\Util\ArrayUtilities;
use Atom\Package\Account\UserAvatar;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Migrate\Crab;
use Atom\Package\Account\DepartmentSub;

/**
 * 获取再指定时间内改变过信息的用户
 * @author haibinzhou@meilishuo.com
 * @since 2015-08-25
 */

class GetUpdateUserInfo extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $queryParams;
    protected $errors = array();

    public function run() {

        $this->_init();
        $queryParams = array();
        if(!empty($this->params['interval']) && !empty($this->params['update_time'])){
            if($this->params['interval'] > 7*24*3600){ //最多只能查7天内变化的
                $this->params['interval'] = 7*24*3600;
                $queryParams['end_time'] = $this->params['update_time']+$this->params['interval'];
                $queryParams['end_time'] = @date('Y-m-d H:i:s',$queryParams['end_time']);
            }
            $queryParams['status'] = array(1,2,3);
            $queryParams['end_time'] = $this->params['update_time']+$this->params['interval'];
            $queryParams['end_time'] = @date('Y-m-d H:i:s',$queryParams['end_time']);
        }

        if ($this->params['update_time']!== '') {
            $queryParams['update_time']=@date('Y-m-d H:i:s',$this->params['update_time']);
        }

        //新库查询
        $result = $this->getUserInfo($queryParams);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = Response::gen_success(Format::outputData($result));
        }

        $this->app->response->setBody($return);
    }

    private function getUserInfo($queryParams){
        $user_info = UserInfo::model()->getDataList($queryParams, 0, 9999);

        foreach($user_info as &$v){
            $v['direct_leader'] = $this->getLeaderId($v['depart_id'],$v['user_id']);
            $v['mail'] = $v['mail'].'@meilishuo.com';
        }

        $user_ids['user_id'] = ArrayUtilities::my_array_column($user_info,'user_id');
        $user_avatar = UserAvatar::model()->getDataList($user_ids,0,9999);
        $avatar = array();
        foreach($user_info as $key=>&$value){   //获取用户头像
            if($value['status'] == 2){
                unset($value['direct_leader']);
            }

            foreach($user_avatar as &$val){
                if($value['user_id'] == $val['user_id']){
                    $avatar['avatar_big'] = BEANSDB_DOMAIN.$val['avatar_big'];
                    $avatar['avatar_src'] = BEANSDB_DOMAIN.$val['avatar_src'];
                    $avatar['avatar_middle'] = BEANSDB_DOMAIN.$val['avatar_middle'];
                    $avatar['avatar_small'] = BEANSDB_DOMAIN.$val['avatar_small'];
                    unset($val['update_time']);
                    $user_info[$key] = array_merge($value,$avatar);
                }
            }
            if(!isset($user_info[$key]['avatar_big'])){
                $value['avatar_big'] = DEFAULT_PHOTO;
                $value['avatar_src'] = DEFAULT_PHOTO;
                $value['avatar_middle'] = DEFAULT_PHOTO;
                $value['avatar_small'] = DEFAULT_PHOTO;
            }
        }

        $depart_info['depart_id'] = ArrayUtilities::my_array_column($user_info,'depart_id');
        $depart_info['depart_id'] = array_unique($depart_info['depart_id']);

        $depart_data = DepartmentInfo::model()->getDataList($depart_info,0,9999);
        $user_info = ArrayUtilities::hashByKey($user_info,'depart_id');

        foreach($user_info as $k=>&$infos){
            if(isset($depart_data[$k])){
                $infos['depart'] = $depart_data[$k]['depart_name'];
            }
            if(!isset($infos['depart'])){
                $infos['depart'] = '';
            }
        }
        $user_info = ArrayUtilities::hashByKey($user_info,'user_id');

        return $user_info;
    }

    //判断当前user_id是否是depart_id的领导，如果是寻找上一级部门的id
    private function getLeaderId($depart_id,$user_id){
        $params['depart_id'] = $depart_id;
        $relation_info = DepartmentRelation::model()->getDataList($params);  //获取部门领导
        $relation_info = array_pop($relation_info);

        if($relation_info['user_id'] == $user_id && $user_id != 1){
            $parent['relation_id'] = $relation_info['parent_relation_id'];
            $parent['is_virtual']  = array(0,1);
            $parent_relation = DepartmentRelation::model()->getDataList($parent);
            $parent_relation = array_pop($parent_relation);

            if($parent_relation['user_id'] == $user_id && $user_id != 1){
                $relationid['relation_id'] = $parent_relation['parent_relation_id'];
                $relationid['is_virtual'] = array(0,1);
                $relation = DepartmentRelation::model()->getDataList($relationid);
                $relation = array_pop($relation);
                if($relation['user_id'] == 1){
                    $relation_info = $relation;
                }else{
                    $this->getLeaderId($relation['depart_id'],$relation['user_id']);
                }
            }else if($parent_relation['user_id'] == 0){
                $sub['relation_id'] = $parent_relation['relation_id'];
                $relation_info = $this->getLeader($sub,$parent_relation);
            }else{
                $relation_info = $parent_relation;
            }
        }else if($relation_info['user_id'] == 0){
            $sub['relation_id'] = $relation_info['relation_id'];
            $relation_info = $this->getLeader($sub,$relation_info);
        }

        $relation_info[] = reset($relation_info);

        return $relation_info['user_id'];
    }

    //获取代理领导
    private function getLeader($sub,&$relation_info){
        $sub_info = DepartmentSub::model()->getDataList($sub);
        if(!empty($sub_info)){
            $sub_info = array_pop($sub_info);
            $relation_info['user_id'] = $sub_info['user_id'];
        }else{   //上级领导离职，又没有代理领导
            $sup_leader['relation_id'] = $relation_info['parent_relation_id'];
            $sup_leader['is_virtual']  = array(0,1);
            $sup_relation = DepartmentRelation::model()->getDataList($sup_leader);
            $sup_relation = array_pop($sup_relation);
            $relation_info['user_id'] = $sup_relation['user_id'];
        }
        return $relation_info;
    }
    /**
     * 参数初始化
     */
    protected function _init()
    {

        $this->rules = array(
            'update_time'=>array(
                'required'=> true,
                'allowEmpty'=> false,
                'type'=>'string',
            ),
            'interval'=>array(
                'type'=>'integer',
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
