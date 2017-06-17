<?php
namespace Speed\Logger;

/**
 * Class Logger
 * @package Speed\Logger
 */
abstract class Logger implements LoggerInterface
{
    const INFO = 'info';
    const ERROR = 'error';


    const VERBOSITY_QUIET = 0;
    const VERBOSITY_NORMAL = 1;
    const VERBOSITY_VERBOSE = 2;
    const VERBOSITY_VERY_VERBOSE = 3;
    const VERBOSITY_DEBUG = 4;

    /**
     * @var array
     */
    protected $verbosityLevelMap = array(
        LogLevel::EMERGENCY => Logger::VERBOSITY_NORMAL,
        LogLevel::ALERT => Logger::VERBOSITY_NORMAL,
        LogLevel::CRITICAL => Logger::VERBOSITY_NORMAL,
        LogLevel::ERROR => Logger::VERBOSITY_NORMAL,
        LogLevel::WARNING => Logger::VERBOSITY_NORMAL,
        LogLevel::NOTICE => Logger::VERBOSITY_VERBOSE,
        LogLevel::INFO => Logger::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => Logger::VERBOSITY_DEBUG,
    );

    /**
     * @var array
     */
    protected $formatLevelMap = array(
        LogLevel::EMERGENCY => self::ERROR,
        LogLevel::ALERT => self::ERROR,
        LogLevel::CRITICAL => self::ERROR,
        LogLevel::ERROR => self::ERROR,
        LogLevel::WARNING => self::INFO,
        LogLevel::NOTICE => self::INFO,
        LogLevel::INFO => self::INFO,
        LogLevel::DEBUG => self::INFO,
    );

    /**
     * @var array
     */
    protected $colorMap = array(
        LogLevel::EMERGENCY => "red+bold",
        LogLevel::ALERT => "red+bold",
        LogLevel::CRITICAL => "red+bold",
        LogLevel::ERROR => "red+bold",
        LogLevel::WARNING => "red+bold",
        LogLevel::NOTICE => "white+bold",
        LogLevel::INFO => "green+bold",
        LogLevel::DEBUG => "yellow+bold",
    );

    /**
     * verbose level
     * @var int
     */
    protected $verbose = Logger::VERBOSITY_NORMAL;

    /**
     * 日志格式
     * @var string
     */
    protected $logFormat = '{log_time}{log_level}{log_message}';

    public function __construct($verbose = Logger::VERBOSITY_NORMAL)
    {
        $this->verbose = $verbose;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY,$message,$context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT,$message,$context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL,$message,$context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR,$message,$context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING,$message,$context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE,$message,$context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO,$message,$context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG,$message,$context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    abstract public function log($level, $message, array $context = array());


    /**
     * Interpolates context values into the message placeholders
     *
     * @author PHP Framework Interoperability Group
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    protected function interpolate($message, array $context)
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace[sprintf('{%s}', $key)] = $val;
            }
        }
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * @param string $logFormat
     */
    public function setLogFormat($logFormat)
    {
        $this->logFormat = $logFormat;
    }


}