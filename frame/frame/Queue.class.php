<?php

namespace Frame;

abstract class Queue {

    protected $app;
    protected $queue = NULL;

    protected $multi = FALSE;
    protected $limit = NULL;
    protected $process_mark = NULL;

    protected $hooks = array(
        'before' => array(),
        'after' => array()
    );

    public function __construct($app) {
        $this->app = $app;
    }

    public function __get($name) {
        return $this->app->$name;
    }

    abstract protected function setQueue();
    abstract protected function setMulti();
    abstract protected function setLimit();

    abstract protected function process($data);

    protected function checkProcessLimit() {
        if (empty($this->limit)) {
            return TRUE;
        }
        if (self::getProcessList($this->process_mark) >= $this->limit) {
            return FALSE;
        }
        return TRUE;
    }

    protected function check() {
        if (empty($this->queue)) 
            throw new \Exception("wrong queue");

        if (!empty($this->limit) && empty($this->process_mark))
            throw new \Exception("process_mask is necessary when set the limit");
    }


    abstract public function run();

    public function start() {
        $this->applyHook('before');

        $this->setQueue();
        $this->setMulti();
        $this->setLimit();
        $this->check();

        $this->run();
        $this->applyHook('after');
    }

    public function hook($name, $callable, $priority = 10)
    {
        if (!isset($this->hooks[$name])) {
            $this->hooks[$name] = array(array());
        }
        if (is_callable($callable)) {
            $this->hooks[$name][(int) $priority][] = $callable;
        }
    }

    public function applyHook($name, $hookArg = null)
    {
        if (!empty($this->hooks[$name])) {
            // Sort by priority, low to high, if there's more than one priority
            if (count($this->hooks[$name]) > 1) {
                ksort($this->hooks[$name]);
            }
            foreach ($this->hooks[$name] as $priority) {
                if (!empty($priority)) {
                    foreach ($priority as $callable) {
                        call_user_func($callable, $hookArg);
                    }
                }
            }
        }
    }
}
