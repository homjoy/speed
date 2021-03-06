<?php
namespace Admin\Modules\Stationery;
use Admin\Package\Core\Config;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Stationery\Stationery;



/**
 * 主页信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class Home extends BaseModule {

    protected $errors = NULL;
    private   $params = NULL;
    protected $checkUserPermission = TRUE;
    protected $config = array('path'=> '/executive/office_supply/approve_type');
    public function run() {

       $this->_init();
       $sonType =$returnData=$son =$fatherType =array();
       $fatherType =Config::getInstance()->getChild($this->config);
      if(is_array($fatherType)){
          foreach($fatherType as $k =>$value){
              $temp = $sonType[$k]= Config::getInstance()->getChild( array('path'=> $this->config['path'].'/'.$k));
              if($k==$this->params['config_key']){
                  $son = $temp;
              }
          }
      }
        $this->params['supply_type'] =array();
        if(is_array($son)){
            foreach($son as $k_s =>$v_s){
                $returnData[$k_s]['s_k'] =$k_s;
                $returnData[$k_s]['s_v'] =$v_s;
                $this->params['supply_type'][]=$k_s;
            }
        }

      //拿到数组
       if(!empty($this->params['supply_name'])){
           $this->params['all'] =1;
       }

       $data = Stationery::getInstance()->getOfficeSupply($this->params);
       if($data&&is_array($data)){
           foreach($data as &$value){
               if(isset($value['supply_type'])&&isset($returnData[$value['supply_type']])){
                   if(!isset($returnData[$value['supply_type']]['count'])){
                       $returnData[$value['supply_type']]['count']=0;
                   }
                   $returnData[$value['supply_type']]['count']+=1;
                   $returnData[$value['supply_type']]['data'][] =$value;
               }

           }
       }else{
           $returnData=array();
       }
       //返回总数
       $return = Response::gen_success($returnData);
       $return['father_type'] = $fatherType;
       $return['son_type']    =    $sonType;
       $return['supply_name'] = $this->params['supply_name'];//keyword
       $return['config_key']  =  $this->params['config_key'];
       $this->app->response->setBody($return);

    }

    private function _init() {

        $this->rules = array(
            'status'  => array(
                'type'    => 'multiId',
                'default' => array(0,1),
            ),
            'supply_name'  => array(
                'type'    => 'string',
            ),
            'config_key'  => array(
                'type'    => 'integer',
                'default' =>1,
            ),
            'match' => array(   // '=' 或者 'like'
                'type'	=> 'string',
                'default'=>'like'
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}