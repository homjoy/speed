<?php

namespace Libs\Log;

class Log
{

    /**
     * @var mixed
     */
    protected $writer;

    /**
     * Constructor
     * @param  mixed $writer
     */
    public function __construct($writer)
    {
        $this->writer = $writer;
    }


    /**
     * Get writer
     * @return mixed
     */
    public function getWriter()
    {
        return $this->writer;
    }


    /**
     * Log message
     * @param  string      $mark
     * @param  mixed       $object
     * @return mixed|bool What the Logger returns
     */
    public function log($mark, $object)
    {
        $message = $this->format($object);
        $currentTime = date("Y-m-d H:i:s", time());
        $message = "[$currentTime]\t" . $message;
        return $this->writer->write($mark, $message);
    }


    /**
     * format log message
     * @param  mixed     $object               The log message
     * @return string    The processed string
     */
    protected function format($object)
    {
        if ($object instanceof \Closure)
        {
            return \Frame\Helper\Util::getClosure($object);
        }
        switch (gettype($object))
        {
            case 'array':
            case 'object':
                $ret = print_r($object, true);
                break;
            default:
                $ret = (string)$object;
        }
        return $ret;
    }
}
