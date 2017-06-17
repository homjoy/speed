<?php
namespace Atom\Modules\Department;

use Atom\Package\Common\Response;
use Atom\Modules\Common\BaseModule;
use Libs\Util\Format;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\UserInfo;
use Libs\Util\ArrayUtilities;
/**
 * 根据user_mail获取部门关系
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-06
 */
class GetDeptRelation extends BaseModule{

    protected $params = array();
    protected $queryParams = array();
    protected $errors = array();

    public function run() {

        if(!$this->_init()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        if(isset($this->params['depart_id']) && !empty($this->params['depart_id'])){
            $this->queryParams['depart_id'] = $this->params['depart_id'];
        }else if(isset($this->params['mail']) && !empty($this->params['mail'])){
            $params['mail'] = $this->params['mail'];
            $params['match'] = 'like';
            $user_info = UserInfo::model()->getDataList($params);
            if(!empty($user_info)){
                $this->queryParams['user_id'] = ArrayUtilities::my_array_column($user_info,'user_id');
            }
        }

        if(empty($this->queryParams)){
            $return = Response::gen_error(50002, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        $list = DepartmentRelation::model()->getDataList($this->queryParams);

        if($list === FALSE) {
            $return = Response::gen_error(10004);
        }else if(empty($list)) {
            $return = Response::gen_error(50002);
        }else {
            $return = Response::gen_success(Format::outputData($list));
        }

        $this->app->response->setBody($return);
    }

    private function _init() {
        $this->rules = array(
            'depart_id'=>array(
                'type'=>'multiId',
            ),
            'mail'=>array(
                'type'=>'string',
            ),
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
        if(empty($this->params['depart_id']) && empty($this->params['mail'])){
            return FAlSE;
        }

        return TRUE;
    }

}