<?php
namespace Admin\Modules\Structure\Depart;
use Admin\Package\Department\Department;
use Admin\Package\Department\DepartRelation;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

/**
 * Created by hongzhou@meilishuo.com
 * User: MLS temp提交上线
 * Date:2015-10-08
 * Time: 下午12:18 title
 */
class AjaxPushDb extends BaseModule {

    protected $errors = NULL;
    public static $VIEW_SWITCH_JSON = TRUE;

    public function run() {
        $push=array();
        //以关系和部门作为判断条件
        $depart_info = Department::getInstance()->getDepartTemp( array('all'=>1,'status'=>array(0,1)));
        $relation_info = DepartRelation::getInstance()->getRelationTempInfo( array('all'=>1,'status'=>array(0,1)));
        if(empty($depart_info)||empty($relation_info)){
            return   $this->app->response->setBody(Response::gen_error(50001,'上线内容为空'));
        }
        $push = Department::getInstance()->createAllDepartInfoByTemp();
        if(empty($push)){
            return   $this->app->response->setBody(Response::gen_error(50001,'上线失败'));
        }
        $this->app->response->setBody(Response::gen_success('上线成功了'));
    }


}