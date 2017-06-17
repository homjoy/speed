<?php
namespace Admin\Modules\Prompt;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Core\Prompt;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS
 * UpdateAddPrompt
 * Date: 18/01/25
 * Time: 下午12:18
 */
class UpdateAddPrompt extends BaseModule
{
    protected $errors = null;
    private $params = null;
    public function run()
    {
       $this->_init();
        //page  count  notify_type   data
       $data = array();
       if(!empty($this->params['notice_id'])){
           $data =  Prompt::getInstance()->getNoticeInfo($this->params);
           $data =is_array($data)?array_pop($data):'';

       }
       $return =  Response::gen_success($data);
       $this->app->response->setBody($return);

    }

    private function _init()
    {

        $this->rules = array(
            'notice_id'=>array(
                'type'=>'integer',
            ),
            'admin_start_time'=>array(
                'type'=>'string',
                'default'=>'0000-00-00 00:00:00',
            ),
            'admin_end_time'=>array(
                'type'=>'string',
                'default'=>date('Y-m-d'),
            ),
            'status'=>array(
                'type'=>'multiId',
                'default'=>array(0,1),
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}