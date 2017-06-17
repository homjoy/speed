<?php

namespace Frame\Speed\View;

class OpenJson extends \Libs\View\Json {

    /**
     * @param $body
     * @return string
     */
    public function format($body) {

        $class = $this->app->router->dispatch();
        $class = $this->checkClass($class);

        if(isset($class::$JSON_SWITCH_VIEW) && $class::$JSON_SWITCH_VIEW === TRUE) {
            //输出 view 数据
            $json_view =  new \Frame\Speed\View\OpenView($this->app);
            return $json_view->format($body);      
        }

        $defaultData = $this->app->response->getDefaultData();
//        if(isset($body['data']) && !empty($defaultData)){
//            $body['data'] = \Libs\Util\Format::outputData($body['data'], $defaultData);
//        }

        //对于自己提供了data 的格式 或者错误 不做处理
//        if(isset($body['data']) || isset($body['error_msg'])){
//            return json_encode($body);
//        }
        //isset检测key 有bug.
        //@see http://stackoverflow.com/questions/9132715/strange-behavior-with-isset-returning-true-for-an-array-key-that-does-not-exis
        if(is_array($body) && (array_key_exists('error_code', $body) || array_key_exists('message',$body))){
            return json_encode($body);
        }

        //使用默认格式包裹data 数据
        //不是错误，对输出做格式兼容
        //$data = isset($body['data']) ? $body['data'] : $body;
        //数组才用默认数据合并.
        // if(is_array($body) && !empty($defaultData)){
        //     $data = \Libs\Util\Format::outputData($body, $defaultData);
        // }else{
        //     $data = $body;
        // }

        // $body = array(
        //     'code' => 200,
        //     'data' => $data,
        // );

        if (isset($body['data']) && !empty($defaultData)) {
            $body['data'] = \Libs\Util\Format::outputData($body['data'], $defaultData);
        }
        
        return json_encode($body);
    }

    private function checkClass($class) {
        if (!class_exists($class)) {

            $array = explode("\\",$class);
            $array[3] = 'Bad';
            $array[4] = 'Badrequest';

            $class = implode("\\", $array);
        }
        return $class;
    }
}