<?php
namespace Atom\Modules\Passport;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Api\Otpauth;

/**
 * OtpauthUpdate
 * @author hepang@meilishuo.com
 * @since 2015-08-24
 */

class OtpauthUpdate extends \Atom\Modules\Common\BaseModule {

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
        if (empty($this->params['user_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = Otpauth::model()->update($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50012);
        }else{
            $return = self::getDataInfo($this->params);
            $return = Response::gen_success(Format::outputData($return, $this->sample));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $allRules = array(
            'user_id'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
            'secret'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'status'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>1,
            ),
        );

        $request = $this->request->POST;
        $this->rules = array_intersect_key($allRules, $request);

        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

    public static function getDataInfo($queryParams = array()){
        if (empty($queryParams)) {
            return FALSE;
        }

        $data = Otpauth::model()->getDataList($queryParams, 0, 1);
        return current($data);
    }

}
