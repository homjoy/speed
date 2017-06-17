<?php
namespace Joint\Package\Common;


/**
 * 注释解析类
 *
 * 可以将Modules下的接口注释转换为HTML文档
 *
 * Class CommentParser
 * @package Joint\Package\Common
 */
class CommentParser {
    /**
     * 项目名称
     * @var string
     */
    protected $projectName = 'Frame';
    /**
     * 模块列表
     * @var array
     */
    protected $modules = array();

    /**
     * API 列表
     * @var array
     */
    protected $apiList = array();

    /**
     * API 文档
     * @var array
     */
    protected $documents = array();

    /**
     * API URL 是否是驼峰规则
     * @var bool
     */
    protected $camelUrl = false;

    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @return \Atom\Package\Common\CommentParser
     */
    public static function instance(){
        if(is_null(static::$instance)){
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $namespaces = explode("\\",(string)__NAMESPACE__);
        //解析项目名称，命名空间的第一个单词
        $this->projectName = $namespaces[0];
        //加载接口列表
        $this->modules = $this->loadModuleClasses();
        //解析注释为文档
        $this->parseComments();
    }

    /**
     * 获取文档列表
     * 默认获取所有文档
     * 获取指定module 下的文档 path = '模块名'
     * 获取指定api 的文档 path='api接口路径，如a/B' (区分大小写).
     * 获取多个api 的文档 array('a/B','user/User')
     *
     * @param null $path
     * @return array
     */
    public function getDocuments($path = null,$exclude = array())
    {
        $documents = $this->documents;
        //排序指定的API 路径
        foreach($documents as $api => $doc){
            foreach($exclude as $ex){
                //如果是a/这样的，排除整个module，
                //否则只排除具体 module/action
                if((substr($ex,-1) == '/' && strpos($api,$ex) !== false)
                    || ($ex == $api)){
                    unset($documents[$api]);
                }
            }
        }

        if(is_null($path)){
            return $documents;
        }

        if(is_string($path) && isset($documents[$path])){
            return $documents[$path];
        }

        $apiList = is_array($path) ? $path : $this->getApiList($path);
        $documents = array();
        foreach($apiList as $api){
            $documents[$api] = $this->documents[$api];
        }

        return $documents;
    }

    /**
     * 获取模块列表
     * @return array
     */
    public function getModules()
    {
        return array_keys($this->modules);
    }

    /**
     * 获取API 列表，可以仅获取指定module 下的 API.
     * @param null $module
     * @return array
     */
    public function getApiList($module = null)
    {
        if(is_null($module) || !isset($this->modules[$module])){
            return $this->apiList;
        }

        $actions = $this->modules[$module];
        if(empty($actions)){
            return array();
        }
        $apiList = array();
        foreach($actions as $a){
            $apiList[] = $module .'/'.$a;
        }

        return $apiList;
    }

    /**
     * 获取模块下的类列表
     * @param $module
     * @return mixed
     */
    protected function getClassesOfModule($module)
    {
        if(!isset($this->modules[$module]))
            throw new \InvalidArgumentException('The Module '. $module . ' does not exist');

        return $this->modules[$module];
    }

    /**
     * 加载模块下的类列表
     * @return array
     */
    protected function loadModuleClasses()
    {
        $modules = array();
        $modulesPath = APP_PATH. '/modules';
        $dir = dir($modulesPath);
        while (false !== ($m = $dir->read())) {  // Read next item in dir
            if($m === '.' || $m === '..')
                continue;
            $modules[$m] = array();

            $actionDir = dir($modulesPath .'/'.$m);
            while(false !== ($a = $actionDir->read())){
                if($a === '.' || $a === '..')
                    continue;
                $matches = array();
                if (preg_match('/^(?<className>.+)\.class\.php$/', $a, $matches)) {
                    $modules[$m][] = $matches['className'];
                }
            }
        }

        return $modules;
    }


    /**
     * 解析HTML注释
     * @param bool $html
     * @return array
     */
    protected function parseComments($html=true)
    {
        foreach($this->modules as $m=> $actions)
        {
            foreach($actions as $a){
                $class = $this->projectName.'\\Modules\\'.$m.'\\'.$a;
                $apiPath = $m .'/'. ($this->camelUrl ? $a : $this->specify($a));
                $reflection = new \ReflectionClass($class);
                $this->apiList[] = $apiPath;

                $comment = $reflection->getDocComment();
                //优先解析类注释
                if(empty($comment)){
                    $comment = $reflection->getMethod('run')->getDocComment();
                }

                if($html && !empty($comment)){
                    $lines = explode(PHP_EOL,$comment);
                    //取消首行和尾行注释
                    array_shift($lines);
                    array_pop($lines);
                    $lines = array_map(function($line){
                        //return trim($line,' 	*');
                        return trim(trim($line),'*');
                    },$lines);

                    $comment = implode('<br/>',$lines);
                }

                $this->documents[$apiPath] = (string)$comment;
            }
        }

        return $this->documents;
    }

    /**
     * @param $camel
     * @param string $glue
     * @return mixed
     */
    public function specify($camel, $glue = '_',$lower=true) {
        $string = preg_replace( '/([a-z0-9])([A-Z])/', "$1$glue$2", $camel );
        return $lower ? strtolower($string) : $string;
    }
}