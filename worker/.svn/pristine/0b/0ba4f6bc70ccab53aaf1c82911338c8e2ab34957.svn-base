<?php
namespace Worker\Modules\Common;

/**
 *
 * Class BaseModule
 * @package Worker\Modules\Common
 */
abstract class BaseModule extends \Frame\Module {

    /**
     * 是否强制检测参数，强制模式下不符合规则会直接抛出异常并自动输出错误
     * @var bool
     */
    protected $forceParamCheck = true;

    /**
     * 参数校验规则
     * @var array
     */
    protected $rules = array();

    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function query()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->GET,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }

    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function post()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->POST,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }

    /**
     * @return \Frame\Speed\Http\ParameterFilter
     */
    protected function request()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new \Frame\Speed\Http\ParameterFilter($this->request->REQUEST,$this->rules,$this->forceParamCheck);
        }
        return $instance;
    }


}