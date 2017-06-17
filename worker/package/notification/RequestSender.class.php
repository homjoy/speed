<?php
namespace Worker\Package\Notification;

/**
 * Class RequestSender
 * @package Worker\Package\Notification
 */
class RequestSender
{
    /**
     * 最大同时发送的数量
     * @var int
     */
    protected $max = 5;
    /**
     * 超时时间
     * @var int
     */
    protected $timeout = 10;
    /**
     * 发送结束的回调
     * @var callable
     */
    protected $callback;

    /**
     * 默认选项，会自动合并每个Request 的特殊选项
     * @var array
     */
    protected $options = array(
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30
    );

    /**
     * 请求头
     * @var array
     */
    protected $headers = array();

    /**
     * 待发送的Request队列
     * @var array
     */
    protected $queue = array();
    /**
     * ch句柄和Request 的关系映射
     * @var array
     */
    protected $handleMap = array();

    function __construct($callback = null)
    {
        if (is_callable($callback)) {
            $this->callback = $callback;
        }
    }

    /**
     * 增加请求
     * @param Request $request
     * @return $this
     */
    public function push(Request $request)
    {
        $this->queue[] = $request;
        return $this;
    }

    /**
     * 增加多个请求
     * @param array $requests
     * @return $this
     */
    public function pushArray(array $requests)
    {
        foreach($requests as $request)
        {
            $this->queue[] = $request;
        }
        return $this;
    }

    /**
     * 清空队列.
     * @return $this
     */
    public function clearQueue()
    {
        $this->queue = array();
        return $this;
    }

    /**
     * 开始发送
     * @param int $max
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function start($max = 5)
    {
        $count = count($this->queue);
        if($count == 0){
            throw new \Exception("发送队列为空！");
        }
        if ($count == 1) {
            return $this->single();
        } else {
            return $this->multi($max);
        }
    }

    /**
     * 同时发送单个
     * @return mixed|null
     */
    protected function single()
    {
        $request = array_shift($this->queue);
        if (empty($request)) {
            return null;
        }

        $ch = curl_init();
        $options = $this->mergeOptions($request);
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        if ($this->callback && is_callable($this->callback)) {
            return call_user_func($this->callback, $output, $info, $request,$error);
        } else {
            return $output;
        }
    }

    /**
     * 同时发送多个
     * @param $max
     * @return bool
     * @throws \Exception
     */
    protected function multi($max)
    {
        if ($max) {
            $this->max = $max;
        }

        // make sure the rolling window isn't greater than the # of urls
        if (count($this->queue) < $this->max)
            $this->max = count($this->queue);
        if ($this->max < 2) {
            throw new \Exception("queue must be greater than 1");
        }

        $master = curl_multi_init();
        for ($i = 0; $i < $this->max; $i++) {
            $ch = curl_init();
            $options = $this->mergeOptions($this->queue[$i]);
            curl_setopt_array($ch, $options);
            curl_multi_add_handle($master, $ch);
            $this->handleMap[(string)$ch] = $i;
        }

        do {
            while (($mrc = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($mrc != CURLM_OK)
                break;
            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($master)) {
                // get the info and content returned on the request
                $info = curl_getinfo($done['handle']);
                $error = curl_error($done['handle']);
                $output = curl_multi_getcontent($done['handle']);
                // send the return values to the callback function.
                $callback = $this->callback;
                if (is_callable($callback)) {
                    $key = (string)$done['handle'];
                    $request = $this->queue[$this->handleMap[$key]];
                    unset($this->handleMap[$key]);
                    call_user_func($callback, $output, $info, $request,$error);
                }
                // start a new request (it's important to do this before removing the old one)
                if ($i < count($this->queue) && isset($this->queue[$i]) && $i < count($this->queue)) {
                    $ch = curl_init();
                    $options = $this->mergeOptions($this->queue[$i]);
                    curl_setopt_array($ch, $options);
                    curl_multi_add_handle($master, $ch);
                    // Add to our request Maps
                    $key = (string)$ch;
                    $this->handleMap[$key] = $i;
                    $i++;
                }
                // remove the curl handle that just completed
                curl_multi_remove_handle($master, $done['handle']);
            }
            // Block for data in / output; error handling is done by curl_multi_exec
            if ($running) {
                curl_multi_select($master, $this->timeout);
            }
        } while ($running);

        curl_multi_close($master);
        return true;
    }

    /**
     * 合并默认选项和Request 的特殊选项
     * @param Request $request
     * @return array
     */
    protected function mergeOptions(Request $request)
    {
        $options = $request->getOptions() + $this->options;
        $headers = $request->getOptions() + $this->headers;
        // set the request URL
        $options[CURLOPT_URL] = $request->getUrl();
        // posting data w/ this request?
        if ($request->isPost()) {
            $options[CURLOPT_POST] = TRUE;
            $options[CURLOPT_POSTFIELDS] = $request->getData();
        }

        if ($headers) {
            $options[CURLOPT_HEADER] = 0;
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        return $options;
    }
}