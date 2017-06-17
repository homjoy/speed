<?php
namespace Atom\Modules\Account;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\UserJobTitle;
/**
 * 职位添加
 * @author hongzhou@meilishuo.com
 * @since 2015-09-08
 */
class CreateUserJobTitle extends \Atom\Modules\Common\BaseModule {


    protected $params = array();
    protected $queryParams = array();
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $this->sample = UserJobTitle::model()->getFields();
        if(!isset($this->queryParams['status'])){
            $this->queryParams['status'] = 1;
        }
        if(isset($this->queryParams['update_time'])&&  !empty($this->queryParams['update_time'])){
            $this->queryParams['update_time'] = date('Y-m-d H:i:s',strtotime($this->queryParams['update_time']));
        }
        $result =  UserJobTitle::model()->insert($this->queryParams);
        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $this->queryParams['title_id'] = $result;
            $return = Response::gen_success(Format::outputData( $this->queryParams, $this->sample));
        }
        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'depart_id'=>array(
                'required'=> true,
                'allowEmpty'=> false,
                'type'=>'integer',
            ),
            'title_name'=>array(
                'required'=> true,
                'allowEmpty'=> false,
                'type'=>'string',
            ),
            'status'=>array(
                'type'=>'integer',
            ),
            'title_info'=>array(
                'type'=>'string',
                'maxLength'=> 300,
            ),
            'memo'=>array(//备注
                'type'=>'string',
                'maxLength'=> 300,
             ),
           'update_time'=>array(//更新时间可以不填写201501010101形式
              'type'=>'string',
            ),
        );
        $this->params   = $this->post()->safe();
        $data = $this->request->POST;
        $this->queryParams = array_intersect_key($this->params,$data);
        $this->errors   = $this->post()->getErrors();
    }


} 