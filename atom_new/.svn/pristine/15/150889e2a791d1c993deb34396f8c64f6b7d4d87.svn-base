<?php
namespace Atom\Modules\Notice;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Notice\NoticeInfo;
use Atom\Package\Notice\NoticeMarked;
use Libs\Util\ArrayUtilities;

/**
 * UserPendingNoticeGet
 * @author hepang@meilishuo.com
 * @since 2015-08-04
 */

class UserPendingNoticeGet extends \Atom\Modules\Common\BaseModule {

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
        if (!empty($this->params['user_id'])) {
            $queryParams['user_id'] = $this->params['user_id'];
        }

        //如果没有用户，则返回错误
        if (empty($queryParams)) {
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取有效提醒，如果没有有效提醒，则报错
        $allNotice = self::_getAllNotice();
        if (empty($allNotice)) {
            $return = Response::gen_error(30001);
            return $this->app->response->setBody($return);
        }

        //获取所有提醒id
        $allIds = ArrayUtilities::my_array_column($allNotice, 'notice_id');
        $queryParams['status']      = 1;
        $queryParams['notice_id']   = $allIds;

        //获取用户已操作提醒
        $markedIds = self::_getMarkedNotice($queryParams);
        //待处理提醒
        $pendingIds = array_diff($allIds, $markedIds);
        $pendingNotice = array();

        foreach ($allNotice as $k => $v) {
            if (empty($v['is_always'])) {
                if (in_array($k, $pendingIds)) {
                    $pendingNotice[$k] = $v;
                }
            }else{
                $pendingNotice[$k] = $v;
            }
        }

        $result = $pendingNotice;

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{

            $return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
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
                'type'=>'integer',
                'default'=>0,
            ),
            'page'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'page_size'=>array(
                'type'=>'integer',
                'default'=>99,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

    /**
     * 有效提醒
     */
    public static function _getAllNotice()
    {
        $queryParams = array(
            'status'    => 1,
            //'in_time'   => date('Y-m-d H:i:s'),
        );

        $noticeData = NoticeInfo::model()->getDataList($queryParams, 0, 99);
        return $noticeData;
    }

    /**
     * 已处理提醒
     */
    public static function _getMarkedNotice($queryParams = array())
    {
        if (empty($queryParams)) {
            return FALSE;
        }

        $markedData = NoticeMarked::model()->getDataList($queryParams, 0, 99);
        $markedIds = array();
        if (!empty($markedData)) {
            $markedIds = ArrayUtilities::my_array_column($markedData, 'notice_id');
        }
        return $markedIds;
    }


}
