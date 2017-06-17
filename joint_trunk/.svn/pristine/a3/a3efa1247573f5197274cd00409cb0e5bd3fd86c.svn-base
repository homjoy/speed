<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Kfmember\MemberInfo;


/**
 * 获取用户信息
 * @author guojiezhu@meilishuo.com
 * @since 2015-08-05
 * wiki http://interfaces.meiliworks.com/show/interface?id=1286&work_id=30
 */

class GetUserLevelInfo extends \Joint\Modules\Common\BaseModule {
    
    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
      
        $data = MemberInfo::getInstance()->getUserLevelInfo($this->params);
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
            ),
            'type' => array(
                'required'      => true,
                'allowEmpty'    => true,
                'type'          => 'string',
                'default'       => 'app',
            ),
        );
        
        $this->rules = $allRules;
        $this->params  = $this->query()->safe();

    }


}