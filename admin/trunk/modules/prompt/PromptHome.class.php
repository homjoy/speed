<?php
namespace Admin\Modules\Prompt;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Core\Prompt;
use Libs\Util\ArrayUtilities;
/**
 * Created by hongzhou@meilishuo.com
 * User: MLS
 * PromptHome
 * Date: 18/01/25
 * Time: 下午12:18
 */
class PromptHome extends BaseModule
{
    protected $errors = null;
    private $params = null;
    private $page_size = 20;
    protected $checkUserPermisssion = TRUE;
    public function run()
    {
        $this->_init();
        //page  count  notify_type   data

        $data =  Prompt::getInstance()->getNoticeInfo($this->params);

        $return =  Response::gen_success($data);

        $count =0;
        if(!$data){
            $this->params['count'] =1;
            $count =  Prompt::getInstance()->getNoticeInfo($this->params);
        }
        if(!empty($count) &&  !is_array($count)){
            $return['count'] = ceil($count/$this->page_size);
        }else{
            $return['count']=0;
        }
        $return['page'] = $this->params['page'];
        $return['search_params'] = $this->params;

        $this->app->response->setBody($return);

    }

    private function _init()
    {

        $this->rules = array(
            'admin_start_time'=>array(
                'type'=>'string',
                'default'=>'0000-00-00 00:00:00',
            ),
            'admin_end_time'=>array(
                'type'=>'string',
                'default'=>date('Y-m-d'),
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'page'=>array(
                'type'=>'integer',
                'default'=>1,
            )
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}