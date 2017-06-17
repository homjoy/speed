<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\Book;
use Admin\Package\Company\Office;
use Admin\Package\Log\Log;
/**
 * AjaxMeetingCancel
 * Class
 *
 * @package Admin\Modules\Meeting
 */
class AjaxMeetingCancel extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    public static $MEET_TYPE= 7;
    public static $VIEW_SWITCH_JSON = TRUE;
    public function run() {

        $this->_init();
        $return =array();
        if( $this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->errors);
            return $this->app->response->setBody($return);
        }

        $meet_book_info = Book::getInstance()->getBooks( $this->params);

        if(!$meet_book_info){
            $this->app->response->setBody(Response::gen_error(50001, "没有这个预定信息"));
            return false;
        }
        //将会议信息删除
        $return = Book::getInstance()->deleteBook($this->params);
        if(!$return){
            $this->app->response->setBody(Response::gen_error(50004, "会议取消失败"));
            return false;
        }
        $return =Response::gen_success($return);
        $this->doLog($this->params);
        $this->app->response->setBody($return);
    }

    protected function doLog($new_param=array(),$old_param='delete'){

        $ret = Log::getInstance()->createLogs(array('user_id'=>$this->user['id'],'handle_id'=>isset($new_param['book_id'])?$new_param['book_id']:'',
            'operation_type'=>$old_param,'after_data'=>json_encode($new_param),'handle_type'=>self::$MEET_TYPE));
        return $ret;
    }
    private function _init() {

        $this->rules = array(
            'book_id'  => array(
                'required'	=> true,
                'allowEmpty' => FALSE,
                'type'    => 'integer',
            )

        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }


}