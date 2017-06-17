<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserJobTitle;


/**
 * 所有职位title
 * @author haibinzhou@meilishuo.com
 * @since 2015-08-24
 */

class UserJobTitleList extends \Atom\Modules\Common\BaseModule {

    protected $params = array();
    protected $queryParams = array();
    protected $errors = array();

	public function run() {
        $this->_init();

        $result =  UserJobTitle::model()->getDataList($this->queryParams,$this->params['offset'],$this->params['limit']);

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
            'title_id'=>array(
                'type'=>'multiId',
            ),
            'depart_id'=>array(
                'type'=>'multiId',
            ),
            'title_name'=>array(
                'type'=>'string',
            ),
            'match'=>array(    //和title_name一起使用 'like'为模糊查询，'='为精确查询
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
                'type'=>'multiId',
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
