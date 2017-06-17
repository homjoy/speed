<?php
namespace Libs\Serviceclient;
/**
 * 处理请求的client
 * @author zx 
 */

class Client {

    public function call($service, $apiName, $params, $opt = array()) {
        $callback = 'solo';
        $request = Request::createRequest();
        $request->setApi($service, $apiName);
        $request->setParam($params);
        $response = Transport::exec(array($callback => $request), $opt);
        return $response[$callback];
    }
}
