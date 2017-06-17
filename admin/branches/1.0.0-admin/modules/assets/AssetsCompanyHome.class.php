<?php
namespace Admin\Modules\Assets;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Assets\AssetsCompany;
/**
 *  AssetsCompanyHome
 * hongzhou@meilishuo.com
 * 2015-09-25
 */
class AssetsCompanyHome extends BaseModule {

    protected $errors = null;
    private $params = null;
    private $page_size = 20;
    protected $checkUserPermisssion = TRUE;
    protected $status_type = array(
        0 =>'无效',
        1 =>'有效',
        2 =>'禁用',

    );
    public function run()
    {
        $this->_init();
        //page  count  notify_type   data
        if (isset($this->params['page'])) {
            if ($this->params['page'] <= 0) {
                $this->params['offset'] = 1;
            } else {
                $this->params['offset'] = ($this->params['page']-1)*$this->page_size;
            }
            $this->params['limit'] =$this->page_size ;
        }

        //查询总的页数
        $count_params = $this->params;
        $count_params['count'] = 1;
        $temp_count =  AssetsCompany::getInstance()->getAssetsCompany($count_params);

        $data  =  AssetsCompany::getInstance()->getAssetsCompany($this->params);
        if(is_array($data)){
           foreach($data as &$value){
               if(isset($value['status'])){
                   $value['status'] = $this->status_type[ $value['status'] ];
               }

           }
        }

        $return = Response::gen_success($data);
        $return['search_params'] = $this->params;
        $return['count'] = ceil(intval($temp_count) / $this->page_size);
        $return['page'] = $this->params['page'];

        $this->app->response->setBody($return);

    }

    private function _init()
    {

        $this->rules = array(
            'status'=>array(
                'type'=>'integer',
                'default'=>1,
            ),
            'name'=>array(
                'type'=>'string',

            ),
            'page'=>array(
                'type'=>'integer',
                'default'=>1,
            )
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }



}