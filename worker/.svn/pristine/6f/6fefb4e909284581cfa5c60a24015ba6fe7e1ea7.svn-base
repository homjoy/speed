<?php
namespace Worker\Scripts\Resume;
use Worker\Package\Utils\Utils;


/**
 * Class Base
 * @package Worker\Scripts\Resume
 */
abstract class SyncBase extends \Frame\Script{
    /**
     * 招聘平台的代码
     * @var string
     */
    protected $platform = '';
    protected $downloadPath = '';
    protected $debug = false;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->downloadPath = TEMP_PATH .'/resumes/'.$this->platform;
        //var_dump(TEMP_PATH .'/resumes/'.$this->platform);exit;
        if(!file_exists($this->downloadPath)){
            mkdir($this->downloadPath,0755,true);
        }
        $this->_init();
    }

    protected function _init(){}

    /**
     * 使用已经下载到本地的简历进行提取测试
     */
    protected function testDownloaded()
    {
        $list = \Worker\Package\Common\ResumeHelper::getDownloadedFiles($this->platform);
        foreach($list as $html){
            $resume = \Worker\Package\Common\ResumeHelper::extractBy($this->platform,$html);
            \Worker\Package\Utils\Utils::dump($resume);
        }
    }

//    protected function getLocalCookie($arr = false)
//    {
//        $cookieJson = (string)@file_get_contents($this->downloadPath.'/cookies.json');
//        if(empty($cookieJson)){
//            return '';
//        }
//        $cookieArray = json_decode($cookieJson,true);
//        if($arr){
//            return $cookieArray;
//        }
//        $cookieStr = array();
//        foreach($cookieArray as $key=>$value){
//            $cookieStr[] = $key.'='.$value;//urlencode($value);
//        }
//
//        return implode('; ',$cookieStr);
//    }
//
//    protected function saveLocalCookie($uri)
//    {
//        $cookieArray = $this->downloader->getClient()->getCookieJar()->allRawValues($uri);
//        return @file_put_contents($this->downloadPath.'/cookies.json',json_encode($cookieArray));
//    }


    protected function log($message)
    {
        if(is_string($message)){
            $message = date('Y-m-d h:i:s').$message ."\n";
            echo $message;
            //$this->response->setBody($message);
        }else{
            if($this->debug){
                Utils::dump($message);
            }else{
                //TODO 决定如何处理吧
                $message = json_encode($message);
            }
        }
    }

    /**
     *
     * @param $validateImgUrl
     * @param int $codeLen
     * @param int $retryCount
     * @return string
     * @throws \Exception
     */
    protected function retryGetVerifyCode($validateImgUrl,$codeLen = 4,$retryCount = 5)
    {
        $validateCode = '';
        //识别验证码~
        while($retryCount-- > 0){
            $this->log('获取登陆验证码');
            $this->downloader->get($validateImgUrl);
            $this->downloader->save('verifyCodeImg.gif');
            $validateCode = \Worker\Package\Common\Api::recognize($this->downloadPath.'/verifyCodeImg.gif');
            if(strlen($validateCode) === $codeLen ){
                $this->log("识别验证码成功！.验证码{$validateCode}");
                return $validateCode;
            }else{
                $this->log("识别验证码出错！.识别到的验证码{$validateCode}");
            }
        }
        if(strlen($validateCode) !== $codeLen){
            $this->log("识别验证码出错！.识别到的验证码{$validateCode}");
            $this->log("识别验证码超过尝试次数，已放弃治疗。");
            die();
        }
        return $validateCode;
    }
}