<?php

namespace Frame\Speed\Test;
use Frame\Speed\Exception\ApiException;
use Frame\Speed\Exception\AssertException;


/**
 * 可测试应用
 *
 * Class TestApplication
 * @package Frame\Speed\Test
 */
class TestApplication {

    /**
     * 待测试的APP 所在目录
     * @var string
     */
    protected $appPath = '';

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var \Speed\Logger\ConsoleLogger
     */
    protected $logger;

    protected $onlyClass = '';

    public $container;

    public function __get($name)
    {
        return $this->container[$name];
    }

    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }

    /**
     * @return static
     */
    public static function instance() {
        static $app = null;
        is_null($app) && $app = new static();
        return $app;
    }

    /**
     *
     */
    private function __construct()
    {
        if(php_sapi_name() !== 'cli'){
            die('只能以Cli 方式运行！');
        }
        //初始化配置读取
        \Frame\ConfigFilter::instance()->setApplication($this);
        //模拟Application 用于保存各种对象
        $this->container = new \Frame\Helper\Set();
        $this->container->singleton('config', function ($c) {
            return new \Frame\Config();
        });


        //解析命令行回显级别
        global $argv;
        $verbose = 0; //静默执行，不输出任何内容
        foreach($argv as $arg){
            if(preg_match('/^-(?<verbose>v{1,4})$/',$arg,$matches) > 0){
                $verbose = strlen((string)$matches['verbose']);
            }
            if(preg_match('/^--only=(?<class>.*?)$/',$arg,$matches) > 0){
                $this->onlyClass = trim((string)$matches['class']);
            }
        }

        //生成日志对象
        $this->logger = new \Speed\Logger\ConsoleLogger($verbose);
        $this->logger->setLogFormat('{log_message}');
    }

    /**
     * @param $appPath
     * @return $this
     * @throws \Exception
     */
    public function setAppPath($appPath)
    {
        if(!file_exists($appPath) || !is_dir($appPath)){
            throw new \Exception("{$appPath}不是一个有效地路径！");
        }
        $this->appPath = realpath($appPath);
        return $this;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }


    /**
     * 程序入口
     * @return bool
     */
    public function runTest()
    {
        if(empty($this->appPath) || empty($this->namespace)){
            $this->logger->critical('请指定appPath 和 namespace');
            return false;
        }
        //记录开始运行时间
        $start = microtime(true);

        //加载所有测试类
        $classes = $this->loadTestClasses();

        //运行每一个测试类里面的测试方法
        foreach($classes as $index=>$className)
        {
            if(!empty($this->onlyClass) && $className != $this->onlyClass){
                continue;
            }

            $class = $this->namespace . $className;
            if(!class_exists($class)){
                $this->logger->error("找不到类{$class}");
                continue;
            }
            //运行该类里面的测试代码
            $this->runTestInClass($class);
        }


        $end = microtime(true);
        //计算总的运行时间
        $totalTime = $end - $start;
        $this->logger->info("运行时间：{$totalTime}秒");
        return true;
    }

    /**
     * 加载测试的类列表
     * @return array
     */
    protected function loadTestClasses()
    {
        $classes = array();
        //测试代码都放在appPath 下tests 目录
        $testClassPath = $this->appPath. '/tests';
        $dir = dir($testClassPath);
        while (false !== ($m = $dir->read())) {
            if($m === '.' || $m === '..')
                continue;

            $subDir = $testClassPath .'/'.$m;
            //如果是类文件
            if(is_file($subDir)){
                if (preg_match('/^(?<className>.+)\.class\.php$/', $m, $matches)) {
                    $classes[] = $matches['className'];
                }
                continue;
            }


            //子模块，只支持一级目录
            $actionDir = dir($subDir);
            while(false !== ($a = $actionDir->read())){
                if($a === '.' || $a === '..')
                    continue;

                $matches = array();
                //提取类名生成完成的命名空间+类名路径
                if (preg_match('/^(?<className>.+)\.class\.php$/', $a, $matches)) {
                    $classes[] = ucfirst($m) .'\\'.ucfirst($matches['className']);
                }
            }
        }

        return $classes;
    }

    /**
     * 运行指定类上面的测试代码
     * @param $class
     */
    protected function runTestInClass($class)
    {
        //实例化对象
        $object = new $class;

        //必须继承ApiTest 才行
        if(!($object instanceof ApiTest)) {
            $this->logger->error("$class 没有继承ApiTest");
            return;
        }
        //传递Logger，哈哈
        $object->setLogger($this->logger);


        $reflection = new \ReflectionClass($class);
        $methods = $reflection->getMethods();

        //依次调用每一个test开头的方法进行测试
        foreach($methods as $method)
        {
            //调用test 开头的测试方法
            if(preg_match('/^test\w/',$method->getName())){
                //类名+方法名的完整路径
                $fullName = $reflection->getName()."::".$method->getName();

                $this->logger->info("{$fullName}");

                //处理方法里面的接口异常或者断言.
                try{
                    $ret = $method->invoke($object);
                    $this->logger->info("接口通过.");
                }catch (\Exception $e){
                    if($e instanceof ApiException || $e instanceof AssertException){
                        $this->logger->debug(print_r($e->getResponse(),true));
                    }
                    $this->logger->error("{$fullName}\t{$e->getMessage()}");
                }

                $this->logger->info(PHP_EOL);
            }
        }

    }

}
