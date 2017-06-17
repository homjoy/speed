<?php
namespace Atom\Modules\Account;

use Atom\Package\Common\Response;
use Atom\Package\Account\UserExtendedRelation;
use Libs\Util\ArrayUtilities;
/**
 * GetUserExtendedRelation
 * @author minggeng@meilishuo.com
 * @since 2015-09-21
 */

class GetUserExtendedRelation extends \Atom\Modules\Common\BaseModule {

    protected $params;
    protected $errors = array();

    public function run() {

        $this->_init();

        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //获取参数
        $queryParams = array();
        if (!empty($this->params['user_id'])) {
            $queryParams['user_id'] = $this->params['user_id'];
        }
        if (!empty($this->params['type'])) {
            $queryParams['type'] = $this->params['type'];
        }

        if(empty($queryParams)){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }

        //查询
        $result = UserExtendedRelation::model()->getList($queryParams);

        if ($result === FALSE) {
            $return = Response::gen_error(50001);
        }elseif (empty($result)) {
            $return = Response::gen_error(30001);
        }else{
            $return = Response::gen_success($result);
        }
        $this->app->response->setBody($return);
    }

    /**
     * 参数初始化
     */
    protected function _init() {
        $this->rules = array(
            'user_id'=>array(
                'allowEmpty'=>false,
                'type'=>'multiId',
            ),
            'type'=>array(
                'type' => 'integer',
            ),
        );
        $this->params   = $this->query()->safe();
        $this->errors   = $this->query()->getErrors();
    }

}
