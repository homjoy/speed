<?php
namespace Atom\Modules\Notice;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Notice\NoticeInfo;
use Atom\Package\Notice\NoticeMarked;

/**
 * NoticeInfoGet
 * @author hepang@meilishuo.com
 * @since 2015-08-04
 */

class NoticeInfoGet extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();
    private $sample;

    public function run() {

        $this->_init();

        $this->sample = NoticeInfo::model()->getFields();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        if (!empty($this->params['status'])) {
            $queryParams['status'] = $this->params['status'];
        }
        if (!empty($this->params['count'])) {
            $queryParams['count'] = $this->params['count'];
        }
        if (!empty($this->params['start_time'])) {
            $queryParams['start_time'] = $this->params['start_time'];
        }
        if (!empty($this->params['end_time'])) {
            $queryParams['end_time'] = $this->params['end_time'];
        }
        if (!empty($this->params['notice_id'])) {
            $queryParams['notice_id'] = $this->params['notice_id'];
        }
        /**
         * 保证后台正常运行代码
         * ********************************************
         */

        if (!empty($this->params['admin_start_time'])&& isset($this->params['start_time'])) {
            unset($queryParams['start_time']);
            $queryParams['admin_start_time'] = $this->params['admin_start_time'];
        }
        if (!empty($this->params['admin_end_time'])&& isset($this->params['end_time'])) {
            unset($queryParams['end_time']);
            $queryParams['admin_end_time'] = $this->params['admin_end_time'];
        }
        if (!empty($this->params['admin_end_time'])||!empty($this->params['admin_start_time'])) {
            $queryParams['status'] = $this->params['status'];

        }
        /**
         *********************************************
         */
        if (!empty($this->params['notice_id'])) {
            $queryParams['notice_id'] = $this->params['notice_id'];
        }
        //查询
        $this->params['page'] = intval($this->params['page']) -1;
        $result = NoticeInfo::model()->getDataList($queryParams, $this->params['page'], $this->params['page_size']);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            if(isset($this->params['count'])&& $this->params['count']==1){
                $return = Response::gen_success($result);
            }else{
                $return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
            }

        }

        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init()
    {
        $this->rules = array(
            'start_time'=>array(
                'type'=>'string',
                'default'=>'2000-01-01 00:00:00',
            ),
            'end_time'=>array(
                'type'=>'string',
                'default'=>date('Y-m-d H:i:s'),
            ),
            'status'=>array(
                'type'=>'multiId',
                'default'=>1,
            ),
            'page'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'count'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
            'page_size'=>array(
                'type'=>'integer',
                'default'=>20,
            ),
            'notice_id'=>array(
                'type'=>'integer',
            ),
            'admin_start_time'=>array(//后台使用
                'type'=>'string',
            ),
            'admin_end_time'=>array(
                'type'=>'string',
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
