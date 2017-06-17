<?php
namespace Speed\Logger;

/**
 * 文件日志
 * Class FileLogger
 * @package Speed\Logger
 */
class FileLogger extends Logger
{
    /**
     * log file save path.
     * @var string
     */
    protected $path = '/tmp/speed_logger';

    /**
     * @param string $path  日志保存路径
     * @param int $verbose  输出级别
     */
    public function __construct($path = '',$verbose = Logger::VERBOSITY_NORMAL)
    {
        parent::__construct($verbose);
        $this->path = $path ? $path : $this->path;
        if (!is_dir($this->path)) {
            @mkdir($this->path);
            @chmod($this->path,0777);
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset($this->verbosityLevelMap[$level])) {
            throw new \InvalidArgumentException(sprintf('The log level "%s" does not exist.', $level));
        }

        if ($this->verbose >= $this->verbosityLevelMap[$level]) {
            //[' . strftime('%Y-%m-%d %T') . ']
            $output =  "[" . strftime('%Y-%m-%d %T') . '] [' . $level . '] '.
                $this->interpolate($message, $context);
            $this->writeln($output);
        }
    }

    /**
     * 写入日志文件
     * @param $message
     */
    protected function writeln($message)
    {
        $filename = $this->path . '/'. date("Ymd") .'.log';
        @file_put_contents($filename, $message . PHP_EOL, FILE_APPEND);
    }
}