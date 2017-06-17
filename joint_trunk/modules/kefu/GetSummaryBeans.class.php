<?php
namespace Joint\Modules\Kefu;

use Joint\Package\Common\Response;
use Joint\Package\Kfmember\MemberInfo;


/**
 *  获取美美豆
 * @author guojiezhu@meilishuo.com
 * @since 2015-09-16
 * wiki http://interfaces.meiliworks.com/show/interface?id=1630&work_id=15
 */

class GetSummaryBeans extends \Joint\Modules\Common\BaseModule {
    
    protected $params = array();
    protected $errors = array();

    public function run(){
        $this->_init();
       
        $data = MemberInfo::getInstance()->getSummaryBeans($this->params);
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
            'v' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'string',
                'default'       => '1',
            ),
            'master' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'string',
                'default'       => '0',
            ),
            'is_mob' => array(
                'required'      => false,
                'allowEmpty'    => true,
                'type'          => 'string',
                'default'       => '0',
            ),
        );
        
        $this->rules = $allRules;//array_intersect_key($allRules, $request);
        $this->params  = $this->query()->safe();
       
        

    }


}