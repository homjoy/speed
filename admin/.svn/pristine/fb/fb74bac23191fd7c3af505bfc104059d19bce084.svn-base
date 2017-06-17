<?php
namespace Admin\Modules\Mail;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Mail\MailGroup;
/**
 * 获取邮箱的列表
 *
 * @author guojiezhu@meilishuo.com
 * @since  2015-11-04
 */
class MailHome extends BaseModule
{

    protected $errors = null;
    private $params = null;
    private $page_size = 20;

    public function run()
    {

        $this->_init();
        //分页控制
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['page'] = 1;
            }
            $this->params['limit'] = $this->page_size;
        }
        if (!$this->params['email']) {
            $mail = explode('@', $this->params['email']);
            $this->params['email'] = trim($mail[0]);
        }
        //查询总的页数
        $count_params = $this->params;
        $count_params['count'] = 1;
        $temp_count = MailGroup::getInstance()->getMailGroupList($count_params);

        //获取邮箱的列表
        $email_list = array();
        if ($temp_count > 0) {
            //获取数据
            $email_list = MailGroup::getInstance()->getMailGroupList($this->params);

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
            'page' => array(
                'type' => 'integer',
                'default' => 1
            ),
            'email' => array(
                'type' => 'string',
            ),

        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }

}