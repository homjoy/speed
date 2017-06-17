<?php
namespace Worker\Modules\Notification;
use Worker\Package\Notification\Notification;
use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;

/**
 * 通知消息批量删除
 *
 * Class MessageDeleteBatch
 * @package Worker\Modules\Notification
 */
class MessageDeleteBatch extends \Worker\Modules\Common\BaseModule {

    /**
     * @var
     */
    protected $pusher;

    protected $msg = array();

    public function run() {
        $this->init();

        $ret = array();

        foreach($this->msg as $msg){
            $ret[] = Notification::model()->deleteByCustom(array(
                'pusher_id' => $this->pusher['pusher_id'],
                'custom_id' => $msg['custom_id'],
                'to_id' => $msg['to_id'],
            ));
        }

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
        );
        $params = $this->post()->safe();
        $this->pusher = Pusher::model()->getByToken($params['token']);
        if(empty($this->pusher)){
            throw new ParameterException('token 无效');
        }


        $msgList = $this->post()->getArray('msg');
        if (empty($msgList)) {
            throw new ParameterException('删除内容不能为空!');
        }

        $formattedData = array();

        foreach ($msgList as $msg) {
            if (!isset($msg['custom_id']) || empty($msg['custom_id'])) {
                throw new ParameterException('custom_id不能为空.');
            }

            $formattedData[] = array(
                'custom_id' => intval($msg['custom_id']),
                'to_id' => isset($msg['to_user_id']) ? intval($msg['to_user_id']) : 0,
            );
        }

        $this->msg = $formattedData;
    }
}