<?php
namespace Worker\Package\Utils;

/**
 * Class Downloader
 * @package Worker\Package\Common
 */
class Downloader{
    /**
     * @var \Goutte\Client
     */
    protected $client;
    protected $userAgent = '';
    protected $referer = '';
    protected $downloadPath = '';

    protected $isLogin = false;

    public function __construct(array $cfg)
    {
        $this->client = new \Goutte\Client();
        $this->userAgent = isset($cfg['userAgent']) ? $cfg['userAgent']
            :'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17';
        $this->referer = isset($cfg['referer']) ? $cfg['referer'] : $this->referer;
        //$this->cookie = isset($cfg['cookie']) ? $cfg['cookie'] : $this->cookie;
        $this->downloadPath = isset($cfg['downloadPath']) ? $cfg['downloadPath']
            : realpath(TEMP_PATH .'/download/');

        //自动保存COOKIES记录
        $emitter = $this->client->getClient()->getEmitter();
        $subscriber = new \GuzzleHttp\Subscriber\Cookie(
            new \Worker\Package\Utils\FileCookieJar($this->downloadPath.'/cookies.txt')
        );
        $emitter->attach($subscriber);
    }

    /**
     * 获取原始的返回内容（默认Crawler 会对HTML 进行规范化）
     * @return string
     * @throws
     */
    public function rawContent()
    {
        $internalResponse = $this->client->getInternalResponse();
        if(empty($internalResponse)){
            throw new \Exception("please request the page first!");
        }
        return (string)$internalResponse->getContent();
    }


    /**
     * 获取请求内容
     * @param $url
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function get($url)
    {
        return $this->request('GET',$url);
    }

    public function post($url,$data)
    {
        return $this->request('POST',$url,$data);
    }

    public function request($type = 'GET',$url,$data = array())
    {
        $this->client->setHeader('User-Agent' , $this->userAgent);
        $this->client->setHeader('Referer',$this->referer);
        $this->client->setHeader('Cookie',$this->cookie);
        return $this->client->request($type, $url, $data);
    }

    public function submit(\Symfony\Component\DomCrawler\Form $form, array $values = array()){
        return $this->client->submit($form,$values);
    }

    /**
     * @return \Goutte\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function checkLogin($callback)
    {
        if(!is_callable($callback)){
            throw new \Exception('not a valid callback function');
        }
        if($this->isLogin){
            return ;
        }
        $this->isLogin = $callback($this);
    }

    /**
     *
     * @param $callback
     * @return mixed
     * @throws \Exception
     */
    public function login($callback)
    {
        if(!is_callable($callback)){
            throw new \Exception('not a valid callback function');
        }
        if(!$this->isLogin){
            $callback($this);
        }
//        return call_user_func($callback,$this,$this->client);
    }

    /**
     * 保存下载内容到指定文件
     * @param $filename
     * @param $content
     * @param bool $force
     * @return bool|int
     */
    public function save($filename,$content = '',$force = true)
    {
        $path = $this->downloadPath .'/'. $filename;
        $content = empty($content) ? $this->rawContent() : $content;
        //如果不是强制写入，则文件存在时直接跳过
        if(!$force){
            return file_exists($path);
        }

        return file_put_contents($path,$content);
    }

    /**
     * 获取文件内容
     * @param $filename
     * @return string
     * @throws \Exception
     */
    public function read($filename)
    {
        $path = $this->downloadPath .'/'. $filename;
        if(!$path || !file_exists($path)){
            throw new \Exception("{$filename}不存在！");
        }
        return file_get_contents($path);
    }

    /**
     * @param string $cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @param string $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @param boolean $isLogin
     */
    public function setIsLogin($isLogin)
    {
        $this->isLogin = $isLogin;
    }

}