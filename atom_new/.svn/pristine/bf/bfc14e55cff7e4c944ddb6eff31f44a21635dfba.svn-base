<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\ArrayUtilities;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentInfo;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Account\UserInfo;
use Atom\Package\User\UserJobRole;
use Atom\Package\Migrate\Crab;
use Frame\Speed\Exception\ParameterException;
/**
 * 获取整个部门领导
 * @author haibinzhou@meilishuo.com
 * @date 2015-08-11
 */
class GetAllDepartLeader extends BaseModule{
	
	private $params = NULL;
    private $relation = array();
	public function run() {

		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}

        if(isset($this->params['user_id']) && !empty($this->params['user_id'])){ //按用户查找
            $user['user_id'] = $this->params['user_id'];
            $user['status'] = array(1,2,3);
            $user_info = UserInfo::model()->getDataList($user); //获取所在部门
            if(empty($user_info)){
                throw new ParameterException("不存在此员工！");
            }
            $user_info = array_pop($user_info);
            $depart['depart_id'] = $user_info['depart_id'];   //获取部门id
        }else if(isset($this->params['depart_id']) && !empty($this->params['depart_id'])){ //按部门id查找
            $depart['depart_id'] = $this->params['depart_id'];
        }else if(isset($this->params['mail']) && !empty($this->params['mail'])){
            $mail['mail'] = $this->params['mail'];
            $user['mail'] = array(1,2,3);
            $user_info = UserInfo::model()->getDataList($mail); //获取所在部门
            if(empty($user_info)){
                throw new ParameterException("不存在此员工！");
            }
            $user_info = array_pop($user_info);
            $depart['depart_id'] = $user_info['depart_id'];   //获取部门id
        }

        $result = $this->leader_info($depart); //获取领导及部门信息

        //如果自己是自己的领导直接去掉
        if(isset($this->params['user_id']) && !empty($this->params['user_id']) && $this->params['user_id'] != 1){
            foreach($result as $key=>$val){
                if($this->params['user_id'] == $val['leader_user_id']){
                    unset($result[$key]);
                }
            }
        }
        if(isset($this->params['mail']) && !empty($this->params['mail']) && $this->params['mail'] != 'yirongxu'){
            foreach($result as $key=>$val){
                if($this->params['mail'].'@meilishuo.com' == $val['mail']){
                    unset($result[$key]);
                }
            }
        }

       // $result = ArrayUtilities::hashByKey($result,'leader_user_id');
        $result = array_values($result);

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else if(empty($result)) {
			$return = Response::gen_error(50002);
		}else {
			$return = Response::gen_success(Format::outputData($result));
		}

        $this->app->response->setBody($return);
	}
    //获取所有的relation领导
    private function leader_info($depart_id){
        $depart = DepartmentRelation::model()->getDataList($depart_id);
        $leader_user_info = array();
        if(!empty($depart)){
            $depart = array_pop($depart);
            if($depart['relation_id'] != 1){
                $relation['relation_id'] = $depart['parent_relation_id'];
                $relation['is_virtual']  = array(0,1);
                $this->relation[] = $depart;
                $allRelation = $this->parent_relation($relation);

                foreach($allRelation as &$value){
                    $u['user_id'] = $value['user_id'];
                    $u_info = UserInfo::model()->getDataList($u);

                    if($u['user_id']==0 || empty($u_info)){
                        $sub['relation_id'] = $value['relation_id'];
                        $this->sub_leader($sub,$value);
                    }
                }

                //领导基本信息
                $leader_user_info = $this->user_info($allRelation);

            }else{
                //直接就是易容
                $CEO[] = $depart;
                $leader_user_info = $this->user_info($CEO);
            }
        }

        return $leader_user_info;
    }

    //获取所有上级，直到易容
    private function parent_relation($params){
        $parent_relation = DepartmentRelation::model()->getDataList($params);
        $parent_relation = array_pop($parent_relation);
        $this->relation[] = $parent_relation;
        if($parent_relation['relation_id'] != 1){
            $param_relation['relation_id'] = $parent_relation['parent_relation_id'];
            $param_relation['is_virtual'] = array(0,1);
            $this->parent_relation($param_relation);
        }

        return $this->relation;
    }

    //获取代理领导
    private function sub_leader($param_sub,&$sub){
        $sub_info = DepartmentSub::model()->getDataList($param_sub);

        if(!empty($sub_info)){
            $sub_info = array_pop($sub_info);
            $sub['sub'] = 1;
            $sub['user_id'] = $sub_info['user_id'];
        }else{
            $sub['whithout'] = 1;//领导离职还没有代理领导
        }

        return $sub;
    }

    //获取领导基本信息
    private function user_info($user){
        $result = array();
        foreach($user as $info){
            if($info['user_id'] != 0){
                $u_id['user_id'] = $info['user_id'];
                $user_info = UserInfo::model()->getDataList($u_id);
                if(!empty($user_info)){
                    $user_info = array_pop($user_info);
                    $departid['depart_id'] = $user_info['depart_id'];
                    $depart_info = DepartmentInfo::model()->getDataList($departid);
                    $depart_info = array_pop($depart_info);

                    $result[$info['relation_id']]['leader_user_id'] = $user_info['user_id'];
                    $result[$info['relation_id']]['leader_user_name'] = $user_info['name_cn'];
                    $result[$info['relation_id']]['mail'] = $user_info['mail'].'@meilishuo.com';
                    $result[$info['relation_id']]['depart_id'] = $depart_info['depart_id'];
                    $result[$info['relation_id']]['depart_name'] = $depart_info['depart_name'];
                    $result[$info['relation_id']]['depart_level'] = $depart_info['depart_level'];
                    $result[$info['relation_id']]['role_id'] = $info['role_id'];
                    if($info['role_id'] == 5){   //用来标示是否是svp
                        $result[$info['relation_id']]['svp'] = 1;
                    }

                }
            }
        }

        return $result;
    }

	private function _init() {
		$this->params = $this->request->GET;

		if(empty($this->params['user_id']) && empty($this->params['depart_id']) && empty($this->params['mail'])) {
			return FALSE;
		}
		
		return TRUE;
	}

}