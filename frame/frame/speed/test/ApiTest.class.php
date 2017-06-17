<?php

namespace Frame\Speed\Test;


/**
 * Class ApiTest
 * @package Frame\Speed\Test
 */
abstract class ApiTest {

    /**
     * @var \Speed\Logger\ConsoleLogger
     */
    protected $logger;

    /**
     * @param \Speed\Logger\ConsoleLogger $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     *
     * @param string $api
     * @param array $params
     * @return ApiResponse
     * @throws \Frame\Speed\Exception\ApiException
     */
    public function atom($api,array $params = array())
    {
        \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 0, 'ip' => '127.0.0.1'));
        $client = new \Libs\Serviceclient\Client();
        $response = $client->call('atom', $api , $params);
        if($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])){
            //记录日志？使用缓存记录请求次数？防止接口挂了的时候无限请求？
            throw new \Frame\Speed\Exception\ApiException($response,"请求接口{$api}出错！");
        }

        return new ApiResponse($response['content']);
    }
}
