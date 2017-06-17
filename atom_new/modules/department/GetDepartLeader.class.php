<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Account\UserInfo;
use Atom\Package\Migrate\Crab;
use Libs\Util\ArrayUtilities;
use Frame\Speed\Exception\ParameterException;
/**
 * 批量获取部门领导
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-23
 */
class GetDepartLeader extends BaseModule{
	private $params = NULL;
	public function run() {

		if(!$this->_init()) {
			$this->app->response->setBody(Response::gen_error(10001));
			return FALSE;
		}

        $result = $this->getDepartLeader($this->params);
		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else {
			$return = Response::gen_success(Format::outputData($result));
		}

        $this->app->response->setBody($return);
	}

    private function getDepartLeader($params = array()){
        $relation_info = DepartmentRelation::model()->getDataList($params,0,9999);

        $result = array();
        if(!empty($relation_info)){
            $user_id['user_id'] = ArrayUtilities::my_array_column($relation_info,'user_id');
            $user_info = UserInfo::model()->getDataList($user_id,0,9999);
            $sub_leader = array();
            //判断当前部门领导人是否离职
            foreach($relation_info as $val){
                if(!isset($user_info[$val['user_id']])){
                    $sub_leader[] = $val['relation_id'];
                }
            }

            if(!empty($sub_leader)){  //是否有代替领导人
                $u_info = $this->leader_sub($sub_leader);
                $user_info = array_merge($user_info,$u_info);
            }

            foreach($user_info as $value){
                foreach($relation_info as $relation){
                    if($value['user_id'] == $relation['user_id']){
                        $result[$relation['depart_id']]['user_id'] = $value['user_id'];
                        $result[$relation['depart_id']]['user_name'] = $value['name_cn'];
                        $result[$relation['depart_id']]['depart_id'] = $value['depart_id'];
                        $result[$relation['depart_id']]['mail'] = $value['mail'];
                    }else{
                        if(isset($value['sub_depart'])){
                            $result[$value['sub_depart']]['user_id'] = $value['user_id'];
                            $result[$value['sub_depart']]['user_name'] = $value['name_cn'];
                            $result[$value['sub_depart']]['depart_id'] = $value['depart_id'];
                            $result[$value['sub_depart']]['mail'] = $value['mail'];
                        }
                    }
                }
            }
        }

        return $result;
    }
    //找替换领导
    private function leader_sub($sub_leader){
        $relation_id['relation_id'] = $sub_leader;
        $sub_info = DepartmentSub::model()->getDataList($relation_id,0,9999);
        $relationid['relation_id'] = ArrayUtilities::my_array_column($sub_info,'relation_id');
        $relation = DepartmentRelation::model()->getDataList($relationid,0,9999);
        $relation = ArrayUtilities::hashByKey($relation,'relation_id');
        foreach($sub_info as &$value){
            if(isset($relation[$value['relation_id']])){
                $value['depart_id'] = $relation[$value['relation_id']]['depart_id'];
            }
        }

        $userid['user_id'] = ArrayUtilities::my_array_column($sub_info,'user_id');
        $u_info = UserInfo::model()->getDataList($userid,0,9999);
        $result = array();
        foreach($sub_info as $k=>$sub){
            foreach($u_info as $key=>$user){
                if($sub['user_id'] == $key){
                    $result[$k] = $user;
                    $result[$k]['sub_depart'] = $sub['depart_id'];
                }
            }
        }

        return $result;
    }

	private function _init() {
        $this->rules = array(
            'depart_id'=>array(
                'type'=>'multiId',
            ),
        );
		$this->params = $this->post()->safe();

		if(empty($this->params['depart_id'])) {
			return FALSE;
		}
		return TRUE;
	}

}
