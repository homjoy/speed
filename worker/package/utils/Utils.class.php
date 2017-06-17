<?php
namespace Worker\Package\Utils;

/**
 *
 * 工具类
 *
 * Class Utils
 * @package Worker\Package\Common
 */
class Utils{

    /**
     * 数据打印
     * @param $data
     */
    static public function dump($data){
        if(php_sapi_name() == 'cli'){
            echo "--------------------我是dump分割线--------------------".PHP_EOL;
            var_dump($data);
            echo "--------------------我是dump分割线--------------------".PHP_EOL;
        }else{
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
        }
    }


    /**
     * 正则提取单条数据，可以包含多个命名分组
     * @param $str
     * @param $regex
     * @param array $groups
     * @return string
     */
    static public function matchOne(&$str,$regex,$groups = array()){
        $matcher = new RegexMatcher($str);
        return $matcher->one($regex,$groups);
    }


    /**
     * 正则匹配全部数据，自动提取命名分组里面的数据
     * @param $str
     * @param $regex
     * @param array $groups
     * @return array
     */
    static public function matchAll(&$str,$regex,$groups = array())
    {
        $matcher = new RegexMatcher($str);
        return $matcher->all($regex,$groups);
    }
}