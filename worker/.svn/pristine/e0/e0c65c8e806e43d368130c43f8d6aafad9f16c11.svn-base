<?php
namespace Worker\Package\Notification;


class Request{
    protected $notification = null; //通知内容
    protected $notifyType = ''; //通知类型
    protected $url = '';
    protected $method = 'GET';
    protected $data = array();
    protected $header = array();
    protected $options = array();

    function __construct($url, $method = 'GET', $data = array(), $header = array(), $options = array())
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;
        $this->header = $header;
        $this->options = $options;
    }

    /**
     * @return null
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param null $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return string
     */
    public function getNotifyType()
    {
        return $this->notifyType;
    }

    /**
     * @param string $notifyType
     */
    public function setNotifyType($notifyType)
    {
        $this->notifyType = $notifyType;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function isPost()
    {
        return strtoupper($this->method) === 'POST';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}