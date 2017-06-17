<?php

namespace Libs\Serviceclient;

/**
 * 处理请求的client
 * 
 * @author zx
 */
class MultiClient {
    private $requestList = array();
    private $responseList = array();
    private $options = array();
    public function call($service, $apiName, $params, $callback, $opt = array()) {
        $request = Request::createRequest();
        $request->setApi($service, $apiName);
        $request->setParam($params);
        $request->setOptions($opt);
        $this->requestList [$callback] = $request;
    }
    public function setopt($opt) {
        $this->options = (array) $opt;
    }
    public function changeRequestMethod($type) {
        Transport::changeMultiRequestMethod($type);
    }
    public function callData() {
        $this->responseList = Transport::exec($this->requestList, $this->options);
        $this->requestList = array();
        return $this->responseList;
    }
    public function __get($callback) {
        if (isset($this->responseList [$callback])) {
            return $this->responseList [$callback];
        }
        else {
            return '';
        }
    }
    public function formatClientData($callback) {
        $Data = $this->__get($callback);
        if ($Data && $Data ['httpcode'] == 200 && $Data ['content'] ['error_code'] == 0) {
            return $Data ['content'] ['data'];
        }
        else {
            return false;
        }
    }
}
