<?php
namespace Worker\Modules\Notification;
use Worker\Package\Notification\Notification;

/**
 * 获取队列中的通知消息
 *
 * Class Pop
 * @package Worker\Modules\Notification
 */
class Pop extends \Worker\Modules\Common\BaseModule {

    public function run() {
        $this->init();

        $ret = Notification::model()->pop();

        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
//        $this->rules = array(
//            'status'=>array(
//                'type'=>'integer',
//            ),
//        );
//        $params = $this->post()->safe();
    }
}