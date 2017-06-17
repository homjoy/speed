<?php
namespace Atom\Modules\Passport;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Api\Otpauth;

/**
 * OtpauthGet
 * @author hepang@meilishuo.com
 * @since 2015-08-25
 */

class OtpauthGet extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = Otpauth::model()->getFields();

        //参数校验
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //判断必填字段
        if (empty($this->params['user_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = Otpauth::model()->getDataList($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50002);
        }else{
            $result = current($result);
            $return = Response::gen_success(Format::outputData($result, $this->sample));
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
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
