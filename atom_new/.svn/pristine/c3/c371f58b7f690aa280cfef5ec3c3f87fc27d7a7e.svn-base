<?php
namespace Atom\Modules\Notice;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Notice\NoticeInfo;

/**
 * NoticeInfoCreate
 * @author hepang@meilishuo.com
 * @since 2015-08-04
 */

class NoticeInfoCreate extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = NoticeInfo::model()->getFields();

        //参数校验
        if($this->post()->hasError()){
            $return = Response::gen_error(10001, '', $this->post()->getErrors());
            return $this->app->response->setBody($return);
        }

        $result = NoticeInfo::model()->insert($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50012);
        }else{
            $this->params['notice_id'] = $result;
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
            'type'=>array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
            'user_id'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'style'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'title'=>array(
                'required'=>true,
                'type'=>'string',
                'default'=>'',
            ),
            'content'=>array(
                'required'=>true,
                'type'=>'string',
                'default'=>'',
            ),
            'link'=>array(
                'type'=>'string',
                'default'=>'',
            ),
            'is_always'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'start_time'=>array(
                'required'=>true,
                'type'=>'string',
                'default'=>0,
            ),
            'end_time'=>array(
                'required'=>true,
                'type'=>'string',
                'default'=>0,
            ),
            'icon'=>array(
                'type'=>'string',
            ),
        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

}
