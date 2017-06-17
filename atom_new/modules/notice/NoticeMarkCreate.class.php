<?php
namespace Atom\Modules\Notice;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Notice\NoticeInfo;
use Atom\Package\Notice\NoticeMarked;

/**
 * NoticeMarkCreate
 * @author hepang@meilishuo.com
 * @since 2015-08-04
 */

class NoticeMarkCreate extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = NoticeMarked::model()->getFields();

        //参数校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        //判断必填字段
        if (empty($this->params['user_id']) || empty($this->params['notice_id'])) {
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = NoticeMarked::model()->insertOrUpdate($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50012);
        }else{
            $this->params['id'] = $result;
            $return = Response::gen_success(Format::outputData($this->params, $this->sample));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'user_id'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
            'notice_id'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
