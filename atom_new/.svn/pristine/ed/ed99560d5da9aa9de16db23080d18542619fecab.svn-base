<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserJobRole;


/**
 * 职位角色
 * @author haibinzhou@meilishuo.com
 * @since 2015-08-24
 */

class UserJobRoleList extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $queryParams = array();
    protected $errors = array();

	public function run() {
        $this->_init();

        $result =  UserJobRole::model()->getDataList($this->queryParams,$this->params['offset'],$this->params['limit']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success(Format::outputData($result));
        }
    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'role_id'=>array(
                'type'=>'multiId',
            ),
            'role_level'=>array(
                'type'=>'multiId',
            ),
            'role_name'=>array(
                'type'=>'string',
            ),
            'match'=>array(    //配合role_name一起使用 'like'为模糊查询，'='为精确查询
                'type'=>'string',
            ),
            'offset' => array(
                'type' => 'integer',
                'default' => 0,
            ),
            'limit' => array(
                'type' => 'integer',
                'default' => 100,
            ),
            'status'=>array(
                'type'=>'integer',
            ),
            'count' => array(   //当为1时获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $data = $this->request->GET;
        $this->queryParams = array_intersect_key($this->params,$data);

        $this->errors   = $this->query()->getErrors();
    }


}