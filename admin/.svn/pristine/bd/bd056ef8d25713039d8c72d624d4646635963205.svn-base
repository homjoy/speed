<?php
namespace Admin\Modules\Itserver;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Itserver\Itserver;

/**
 * 获取官方邮箱的列表
 *
 * @author hongzhou@meilishuo.com
 * @since  2015-01-19
 */
class OfficialMailHome extends BaseModule
{

    protected $errors = null;
    private $params = null;
    private $page_size = 20;
    protected $checkUserPermisssion = TRUE;
    public function run()
    {

        $this->_init();
        //分页控制
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['offset'] = 1;
            } else {
                $this->params['offset'] = ($this->params['page']-1)*$this->page_size;
            }
            $this->params['limit'] =$this->page_size ;
        }

        if(!$this->params['mail_name']){
            $mail = explode('@',$this->params['mail_name']);
            $this->params['mail_name'] = trim($mail[0]);
        }
        //查询总的页数
        $count_params = $this->params;
        $count_params['count'] = 1;
        $temp_count = Itserver::getInstance()->getOfficialMail($count_params);

        //获取邮箱的列表
        $email_list = array();
        if($temp_count >0){
            //获取数据
            unset( $count_params['count'] );
            $email_list =  Itserver::getInstance()->getOfficialMail($count_params);

            $return['data'] = $email_list;
        }
        $return['search_params'] = $this->params;
        $return['count'] = ceil(intval($temp_count) / $this->page_size);

        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);

    }



    private function _init()
    {

        $this->rules = array(
                'status' => array(
                    'type' => 'integer',
                    'default' => 1
                ),
                'page'   => array(
                    'type' => 'integer',
                    'default' => 1
                ),
                'mail_name'=>array(
                    'type'=>'string',
                ),
                'match'=>array(
                    'type'=>'string',
                    'default'=>'like'
                ),


        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}