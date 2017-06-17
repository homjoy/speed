<?php
namespace Worker\Modules\Notification;

use Worker\Package\Notification\Pusher;
use Frame\Speed\Exception\ParameterException;

/**
 * 创建推送人
 *
 * Class CreatePusher
 * @package Worker\Modules\Notification
 */
class CreatePusher extends \Worker\Modules\Common\BaseModule {

    protected $pusher = array();

    public function run() {
        $this->init();

        $ret = Pusher::model()->create($this->pusher);

        return $this->app->response->setBody($ret);
    }

    /**
     *
     */
    protected function init()
    {
        //参数的校验规则，无需检测是否有错误，自动输出错误信息
        $this->rules = array(
            'app'=>array(
                'required' => true,
                'type'=>'string',
                'maxLength' => 20,
            ),
            'module'=>array(
                'required' => true,
                'type'=>'string',
                'maxLength' => 20,
            ),
            'name'=>array(
                'required' => true,
                'type'=>'string',
                'maxLength' => 255,
            ),
            'default_template' => array(
                'type' => 'integer',
            ),
        );

        $params = $this->post()->safe();

        $this->pusher = Pusher::model()->getByModule($params['app'],$params['module']);
        if(!empty($this->pusher)){
            throw new ParameterException('已存在，请勿重复创建.');
        }

        $this->pusher = $params;
    }
}