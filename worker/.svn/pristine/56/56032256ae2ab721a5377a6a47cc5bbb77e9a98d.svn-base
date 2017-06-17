<?php
namespace Worker\Package\Common;

/**
 * 各种API都可以通过这里来封装请求
 * Class Api
 * @package Worker\Package\Common
 */
class Api {
    const RESUME_SAVE = 'recruit/resumeSave';
    const RESUME_GET = 'recruit/resumeGet';
    /**
     * 验证码识别API
     * 来源：http://hayageek.com/extract-text-from-image-php/
     */
    const IDOL_V1 = 'https://api.idolondemand.com/1/api/sync/ocrdocument/v1';

    private function __construct(){}
    /**
     *
     * 验证码识别
     * @param $captchaFile
     * @return string
     * @throws \Exception
     */
    public static function recognize($captchaFile)
    {
        if(!file_exists($captchaFile) || !is_file($captchaFile)){
            throw new \Exception("{$captchaFile} is not a valid image file");
        }

        $data = array(
            'apikey' => '83c9208b-c536-481a-aa91-5a79aab324a0',
            'mode' => 'document_photo',
            //'file' =>'@'.$captchaFile
            'file' => curl_file_create($captchaFile),
        );
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        //curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_setopt($ch, CURLOPT_URL,Api::IDOL_V1);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result=curl_exec ($ch);
        curl_close ($ch);
        if(curl_errno($ch)){
            return curl_error($ch);
        }
        if(empty($result)){
            return '';
        }

        $result = json_decode($result,true);
        if(isset($result['text_block']) && isset($result['text_block'][0]['text'])){
            return (string)$result['text_block'][0]['text'];
        }
        return '';
    }
}