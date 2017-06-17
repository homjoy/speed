<?php
namespace Admin\Modules\Im\App;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Im\ImAppVersion;

class AppVersion extends BaseModule {

    private $page = 1;
    private $limit = 3;

    public function run(){
        $this->_init();
        $res = array();
        $res['page'] = ($this->params['page']>0) ? $this->params['page'] : $this->page;
        $offset = ($res['page'] - 1) * $this->limit;
        $res['count'] = ImAppVersion::getInstance()->countByPararm();
        $res['list'] = ImAppVersion::getInstance()->getDataList(array(),$offset, $this->limit);

        $this->app->response->setBody($res);
    }
    private function _init()
    {

        $this->rules = array(
                'page'   => array(
                    'type' => 'integer',
                    'default' => 1
                ),

        );
        $this->params = $this->request()->safe();
        $this->errors = $this->request()->getErrors();
    }
}
