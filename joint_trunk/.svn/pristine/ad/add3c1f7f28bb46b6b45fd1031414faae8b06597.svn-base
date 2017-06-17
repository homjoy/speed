<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Kfmember\MemberInfo;

/**
 * 获取会员等级变更记录
 * Class GetUserGradeLog
 * @package Joint\Modules\Kefu
 * @author yongzhao
 * @date 2016-01-20
 */
class GetUserGradeLog extends \Joint\Modules\Common\BaseModule {

    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
        $data = MemberInfo::getInstance()->getGradeLog($this->params);
        if ($data === FALSE) {
            $data = Response::gen_error(60001);
        }
        return $this->app->response->setBody($data);
    }

    private function _init(){
        $allRules = array(
            'user_id' => array(
                'required'      => true,
                'allowEmpty'    => false,
                'type'          => 'string',
                'default'       => '',
            )
        );

        $this->rules = $allRules;
        $this->params  = $this->query()->safe();



    }


}