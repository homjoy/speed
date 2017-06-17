<?php
namespace Atom\Modules\A;

/**
 * 示例代码
 *
 * Class Example
 * @package Atom\Modules\A
 */
class Example extends \Atom\Modules\Common\BaseModule {

    public function run() {
        $this->init();


        // 这里任意处理你自己的数据即可，
        // 不再需要处理数据监测、或者其他什么
        $data = array();

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
//            'param'=>array(
//                'required'=> true,  //参数是否必须
//                'allowEmpty' => false,    //是否允许为空，在提供参数的情况下，是否允许为空串
//                'type'=>'string', //支持string、integer、float
//                'min'=>0,   //整数、浮点数适用
//                'max'=>20,  //整数、浮点数适用
//                'minLength' => 1, //字符串适用
//                'maxLength' => 20, //字符串适用
//                'regex'=> '\d+',    //正则表达式检测
//                'enum' => array('高中','大专','本科','硕士','博士'),  //枚举类型
//                'phone' => true,  //手机号格式
//                'email' => true,  //完整的邮箱格式
//                //有效的回调都支持~ ，只有参数为required 的时候才会调用进行检测
//                //'callback' => '\Atom\Package\Recruit\Resume::checkSource',
//                'callback' => function($value,$filter){
//                    if(检测$value){
//                        return false;
//                    }
//                    return true;
//                }
//            ),
        );

        //过滤之后直接取值
        //$this->query()->safe('param','integer',0);
        $this->source = $this->query()->safe('source');
        $this->page = $this->query()->safe('page','integer',$this->page);
        $this->pageSize = $this->query()->safe('pageSize','integer',$this->pageSize);
    }
}