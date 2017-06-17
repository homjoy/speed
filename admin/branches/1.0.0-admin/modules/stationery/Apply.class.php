<?php
namespace Admin\Modules\Stationery;
use Admin\Package\Core\Config;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Stationery\Stationery;
use Admin\Package\Account\UserInfo;



/**
 * 主页信息
 * @author hongzhou@meilishuo.com
 * @since 2016-01-23 下午12:53:13

 *
 */
class Apply extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    protected $checkUserPermission = TRUE;
//    public static $VIEW_SWITCH_JSON = TRUE;
    private $page_size = 20;

    public function run() {

        $this->_init();
        $return = array();
        $this->params = array_filter($this->params);
        $this->params['offset']  = intval($this->params['page']-1)* $this->page_size;
        $this->params['limit']  =  $this->page_size;
        //拿到物品名称 申请人 数量 创建时间  是否审批成功

        $result =  Stationery::getInstance()->getOrderOfficeSupply($this->params);


        foreach($result as &$value){

                switch($value['status']){
                    case 1:
                        $value['status'] ='新建';
                        break;
                    case 2:
                        $value['status'] ='待接收';
                        break;
                    case 3:
                         $value['status'] ='处理中';
                        break;
                    case 4:
                        $value['status'] ='完成';
                        break;
                    case 5:
                        $value['status'] ='驳回';
                        break;
                    case 6:
                    default:
                        $value['status'] ='失效';
                        break;

                }
                switch($value['output']){
                    case 1:
                        $value['output'] ='发放';
                        break;
                    case 2:
                        $value['output'] ='未发放';
                        break;
                    case 3:
                        $value['output'] ='3不发放';
                        break;


                }
                $t = $d  = null;
                if(isset($value['detail'])){
                    foreach($value['detail'] as &$v){
                        $t .=   '[';
                        $t .= isset($v['supply_name'])?$v['supply_name']:'';
                        $t .=   ']';
                        $d .=  '[';
                        $d .=isset($v['apply_number'])?$v['apply_number']:'';
                        $d .=  ']';
                    }
                }
            $value['supply_name'] = $t;
            $value['apply_number'] = $d;

        }
        $this->params['count'] =1;
        $count =  Stationery::getInstance()->getOrderOfficeSupply($this->params);
        $return =  Response::gen_success($result);
        if($count){
            $return['count'] = ceil($count/$this->page_size);
        }else{
            $return['count']='';
        }
        $return['search_params'] = $this->params;
        $return['page'] = $this->params['page'];
        $this->app->response->setBody($return);

    }

    public function _init() {

        $this->rules = array(
            'create_time' => array(
                'type' => 'string',
               
//                'required'	=> true,
            ),
            'end_time' => array(
                'type' => 'string',
//                'required'	=> true,
            ),
//            'user_id' => array(
//                'type' => 'integer',
//            ),
            'page' => array(
                'type'    => 'integer',
                'default' => 1
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}