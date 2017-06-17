<?php
namespace WorkFlowApi\Modules\A;

/**
 * 示例代码
 *
 * Class Example
 * @package Atom\Modules\A
 */
class Example extends \WorkFlowApi\Modules\Common\BaseModule {

    public function run() {
//        $this->init();


        // 这里任意处理你自己的数据即可，
        // 不再需要处理数据监测、或者其他什么
        $data = array('data' => 'hello');

        /**
         * DO SOME YOUR LOGIC
         */
        return $this->app->response->setBody($data);
    }

    /**
     *
     */
    protected function init()
    {
        //设置默认的输出格式，做数据合并用
        $this->response->setDefaultData(array(
            'id'=> 0,
            'user_id'=> 0,
        ));

        //参数的校验规则，无需检测是否有错误，自动输出错误信息
        $this->rules = array(
            'workYear'=>array(
                'type'=>'integer',
            ),
        );

    }
}
