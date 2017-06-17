<?php
namespace Frame\Speed\Lib;

/**
 * Class Api
 * @package Frame\Speed\Lib
 */
class Api
{

    /**
     * @var \Libs\Serviceclient\Client
     */
    private static $client = null;
    private static $app = null;
    /**
     * client 单例方法.
     * @return \Libs\Serviceclient\Client
     */
    private static function createClient()
    {
        if (is_null(static::$client)) {
            \Libs\Serviceclient\ClientHeaderCreator::setInfos(array('user_id' => 0, 'ip' => '127.0.0.1'));
            static::$client = new \Libs\Serviceclient\Client();
        }
        if (is_null(static::$app)) {
            if(php_sapi_name() == 'cli'){
                static::$app = \Frame\Application::instance();
            }else{
                static::$app = \Frame\Speed\Application::instance();
            }
        }

        return static::$client;
    }

    /**
     * atom接口调用
     * @param string $api
     * @param array $params
     * @param boolean $rawOutput 获取接口原始数据
     * @return mixed
     * @throws \Exception
     */
    public static function atom($api, array $params, $rawOutput = false)
    {
        $client = static::createClient();
        $response = $client->call('atom', $api, $params);
        $params_log = array(
            'params'    => $params,
            'api'       => $api,
            'response'  => $response,
            'user_id'   => static::$app->currentUser['user_id'],
        );
        if ($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])) {
            //记录日志？使用缓存记录请求次数？防止接口挂了的时候无限请求？
            static::$app->log->log('atom/atom_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response, "请求接口{$api}出错！");
        }

        if ($rawOutput) {
            return $response['content'];
        }

        //接口正常返回，但是
        if ($response['content']['code'] == 400) {
            return array();
        }
        if ($response['content']['code'] != 200) {
            static::$app->log->log('atom/atom_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response['content'], "{$api}接口异常,请检查.");
        }

        return $response['content']['data'];
    }

    /**
     * WORKER接口调用.
     * @param string $api
     * @param array $params
     * @param boolean $rawOutput 获取接口原始数据
     * @return array
     * @throws \Frame\Speed\Exception\ApiException
     */
    public static function worker($api, array $params, $rawOutput = false)
    {
        $client = static::createClient();
        $response = $client->call('worker', $api, $params);
        $params_log = array(
            'params'    => $params,
            'api'       => $api,
            'response'  => $response,
            'user_id'   => static::$app->currentUser['user_id'],
        );
        if ($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])) {
            //记录日志？使用缓存记录请求次数？防止接口挂了的时候无限请求？
            static::$app->log->log('worker/worker_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response, "请求接口{$api}出错！");
        }

        if ($rawOutput) {
            return $response['content'];
        }

        //接口正常返回，但是
        if ($response['content']['code'] == 400) {
            return array();
        }
        if ($response['content']['code'] != 200) {
            static::$app->log->log('worker/worker_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response['content'], "{$api}接口异常,请检查.");
        }

        return $response['content']['data'];
    }

    /**
     * WORKFLOW接口调用.
     * @param string $api
     * @param array $params
     * @param boolean $rawOutput 获取接口原始数据
     * @return array
     * @throws \Frame\Speed\Exception\ApiException
     */
    public static function workflow($api, array $params, $rawOutput = false)
    {
        $client = static::createClient();
        if(!isset($params['speed_user_id'])){
            $params['speed_user_id'] = static::$app->currentUser['user_id'];
        }
        //$params['speed_token'] = trim($_COOKIE['speed_token']);
        $response = $client->call('workflowapi', $api, $params);
        $params_log = array(
            'params'    => $params,
            'api'       => $api,
            'response'  => $response,
            'user_id'   => static::$app->currentUser['user_id'],
        );
        if ($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])) {
            //记录日志？使用缓存记录请求次数？防止接口挂了的时候无限请求？
            static::$app->log->log('workflow/workflow_error_log', json_encode($params_log));
            return false;
            //throw new \Frame\Speed\Exception\ApiException($response, "请求接口{$api}出错！");
        }

        if ($rawOutput) {
            return $response['content'];
        }

        if ($response['content']['code'] != 200) {
            $content = json_encode($response['content']);
            static::$app->log->log('workflow/workflow_error_log', json_encode($params_log));
            return false;
            //throw new \Frame\Speed\Exception\ApiException($response['content'], "{$api}接口异常,请检查.".$content);
        }

        return $response['content']['data'];
    }

    /**
     * JOINT接口调用.
     * @param string $api
     * @param array $params
     * @param boolean $rawOutput 获取接口原始数据
     * @return array
     * @throws \Frame\Speed\Exception\ApiException
     */
    public static function joint($api, array $params, $rawOutput = false)
    {
        $client = static::createClient();//var_dump($api, $params);exit;
        $response = $client->call('joint', $api, $params);//var_dump($response);exit;
        $params_log = array(
            'params'    => $params,
            'api'       => $api,
            'response'  => $response,
            'user_id'   => static::$app->currentUser['user_id'],
        );
        if ($response['httpcode'] !== 200 || !isset($response['content']) || empty($response['content'])) {
            //记录日志？使用缓存记录请求次数？防止接口挂了的时候无限请求？
            static::$app->log->log('joint/joint_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response, "请求接口{$api}出错！");
        }

        if ($rawOutput) {
            return $response['content'];
        }

        if ($response['content']['code'] != 200) {
            $content = json_encode($response['content']);
            static::$app->log->log('joint/joint_error_log', json_encode($params_log));
            throw new \Frame\Speed\Exception\ApiException($response['content'], "{$api}接口异常,请检查.".$content);
        }

        return $response['content']['data'];
    }
}
