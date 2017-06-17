<?php
namespace Worker\Package\Common;
use Worker\Package\Extractor\Job51Extractor;
use Worker\Package\Extractor\TC58Extractor;
use Worker\Package\Extractor\LieTouExtractor;
use Worker\Package\Extractor\ZhaoPinExtractor;


/**
 * 简历处理的帮助类
 * Class Helper
 * @package Worker\Package\Resume
 */
class ResumeHelper{
    private function __construct(){}

    /**
     * 简历合并
     * @param $old
     * @param $new
     * @return array
     */
    public static function merge($old,$new)
    {
        if(empty($old)){
            return $new;
        }



        return array();
    }

    /**
     * @param $platform
     * @param array $exclude
     * @param string $fileExt
     * @return array
     */
    static public function getDownloadedFiles($platform,$exclude=array(),$fileExt = 'html')
    {
        $tmpDir = TEMP_PATH . '/resumes/'.$platform .'/';
        $files = scandir($tmpDir);
        $htmlList = array();
        foreach($files as $f){
            //不被排除并且是指定的扩展名
            if(!in_array($f,$exclude) && preg_match("/\.$fileExt$/",$f)){
                $htmlList[] = file_get_contents($tmpDir . $f);
            }
        }
        return $htmlList;
    }

    static public function getResumeById($platform,$id)
    {
        $tmpDir = TEMP_PATH . '/resumes/'.$platform .'/';
        $filename = $tmpDir.'resume_'.$id.'.html';
        return @file_get_contents($filename);
    }

    /**
     * 由HTML 提取指定的
     * @param $platform
     * @param string $html
     * @return array
     * @throws \Exception
     */
    static public function extractBy($platform,&$html = '')
    {
        if(empty($html)){
            return array();
        }

        switch($platform){
            case '51job':
                $resume = Job51Extractor::resume($html);
                break;
            case '58tc':
                $resume = (new TC58Extractor($html))->resume();
                break;
            case 'lietou':
                $resume = LieTouExtractor::resume($html);
                break;
            case 'zhaopin':
                $resume = ZhaoPinExtractor::resume($html);
                break;
            default:
                throw new \Exception("{$platform} resume extractor not exist!");
        }

        return $resume;
    }
}