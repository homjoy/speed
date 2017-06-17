<?php
namespace Worker\Modules\Notification;
use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;

/**
 * 通知消息状态变更
 *
 * Class StatusChanged
 * @package Worker\Modules\Notification
 */
class StatusChanged extends \Worker\Modules\Common\BaseModule {

    protected $notifyId;
    protected $status;

    public function run() {
        $this->init();

        $ret = Notification::model()->statusChanged($this->notifyId,$this->status);

        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
        $this->rules = array(
            'notify_id' => array(
                'required' => true,
                'type'=>'integer',
            ),
            'status'=>array(
                'type'=>'string',
            ),
        );
        $params = $this->post()->safe();

        $this->notifyId = $params['notify_id'];
        if(!isset(Notification::$statusMap[$params['status']])){
            throw new ParameterException("status不正确.");
        }

        $this->status = Notification::$statusMap[$params['status']];
    }
}