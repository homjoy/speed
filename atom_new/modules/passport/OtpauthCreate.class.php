<?php
namespace Atom\Modules\Passport;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Api\Otpauth;

/**
 * OtpauthCreate
 * @author hepang@meilishuo.com
 * @since 2015-08-24
 */

class OtpauthCreate extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = Otpauth::model()->getFields();

        //参数校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        //判断必填字段
        if (empty($this->params['user_id']) || empty($this->params['secret'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = Otpauth::model()->insert($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50012);
        }else{
            $this->params['id'] = $result;
            $return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'user_id'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
            'ip'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'secret'=>array(
                'required'=>true,
                'type'=>'string',
                'default'=>'',
            ),
            'expire_time'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
