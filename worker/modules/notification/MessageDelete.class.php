<?php
namespace Worker\Modules\Notification;
use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;

/**
 * 通知消息删除
 *
 * Class MessageDelete
 * @package Worker\Modules\Notification
 */
class MessageDelete extends \Worker\Modules\Common\BaseModule {

    /**
     * @var
     */
    protected $pusher;
    protected $customId;
    protected $toUserId;

    public function run() {
        $this->init();

        $ret = Notification::model()->deleteByCustom(array(
            'pusher_id' => $this->pusher['pusher_id'],
            'custom_id' => $this->customId,
            'to_id' => $this->toUserId,
        ));

        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
        $this->rules = array(
            'token' => array(
                'required' => true,
                'type'=>'string',
            ),
            'custom_id'=>array(
                'required' => true,
                'type'=>'integer',
            ),
            'to_user_id'=>array(
                'required' => false,
                'type'=>'multiId',
            ),
        );
        $params = $this->post()->safe();

        $this->pusher = Pusher::model()->getByToken($params['token']);
        if(empty($this->pusher)){
            throw new ParameterException('token 无效');
        }

        $this->customId = $params['custom_id'];
        if(empty($this->customId)){
            throw new ParameterException("custom_id不能为空!");
        }

        $this->toUserId = $params['to_user_id'];
    }
}