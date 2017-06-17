<?php
namespace Atom\Modules\Notice;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Notice\NoticeInfo;

/**
 * NoticeInfoUpdate
 * @author hepang@meilishuo.com
 * @since 2015-08-04
 */

class NoticeInfoUpdate extends \Atom\Modules\Common\BaseModule {

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

        //排除空参数
        $filterArray = array('type', 'user_id', 'style', 'title', 'content', 'link', 'start_time', 'end_time','icon');
        foreach ($filterArray as $value) {
            if (empty($this->params[$value])) {
                unset($this->params[$value]);
            }
        }

        $result = NoticeInfo::model()->insertOrUpdate($this->params);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(50012);
        }else{
            $result = self::getNoticeInfo($this->params['notice_id']);
            $return = Response::gen_success(Format::outputData($result, $this->sample));
        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'notice_id' => array(
                'required'=>true,
                'type'=>'integer',
                'default'=>0,
            ),
            'type'=>array(
                'type'=>'integer',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'user_id'=>array(
                'type'=>'integer',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'style'=>array(
                'type'=>'string',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'title'=>array(
                'type'=>'string',
                'default'=>'',
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'content'=>array(
                'type'=>'string',
                'default'=>'',
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'link'=>array(
                'type'=>'string',
                'default'=>'',
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'is_always'=>array(
                'type'=>'integer',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'start_time'=>array(
                'type'=>'string',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'end_time'=>array(
                'type'=>'string',
                'default'=>0,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
                'required'=> false,
                'allowEmpty'=> false,
            ),
            'icon'=>array(
                'type'=>'string',
            ),
        );
        $this->params = $this->post()->safe();
        $this->errors = $this->post()->getErrors();
    }

    public static function getNoticeInfo($id = 0){
        if (empty($id)) {
            return FALSE;
        }

        $queryParams['notice_id'] = $id;
        $data = NoticeInfo::model()->getDataList($queryParams, 0, 1);
        return current($data);
    }
}
