<?php
namespace Admin\Modules\Assets;

use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Assets\AssetsSupply;
/**
 *  AssersApplyHome
 * hongzhou@meilishuo.com
 * 2015-09-25
 */
class AssetsSupplyHome extends BaseModule {
    protected $all = 1;
    protected $status = 1;
    public static $ROOT=0;
    public static $ROOT_TYPE=0;
    public function run() {
        $apply =array();
        $apply = AssetsSupply::getInstance()->getAssetsSupply(
            array(
                'limit'=>99999,
                'status'=>$this->status
            )
        );
//        $apply =array(
//            1=>array(//顶级节点
//                "id" =>1,
//                "name"=>'父亲',
//                "type"=>1,
//                "memo"=>'父亲',
//                "status"=>1,
//                "pid"=>0,
//            ),
//            2=>array(//顶级节点
//                "id" =>2,
//                "name"=>'儿子',
//                "type"=>2,
//                "memo"=>'儿子',
//                "status"=>1,
//                "pid"=>1,
//            ),
//
//        );
        $data = array('id'=>$apply);
        $d_tree =array();
        if(is_array($apply)){
            foreach($apply as $key=>$value){
                if($value['pid']==self::$ROOT){
                    $d_tree[]= $this->applyTree($value['id'],$data);
                }

            }
        }
        $p =array(//顶级节点
            "id" =>self::$ROOT,
            "name"=>'固定资产',
            "type"=>self::$ROOT_TYPE,
            "memo"=>'固定资产',
            "status"=>$this->status,
            "pid"=>'',
            'child'=>is_array($d_tree)?$d_tree:'',
        );
        if(empty($p)){
            return $this->app->response->setBody(Response::gen_success(array()));
        }
        $this->app->response->setBody(Response::gen_success(array($p)));
    }

    private  function applyTree($depart_id,$data){//depart_id


        $params= $child =  array();
        if(is_array($data['id'])){
            foreach($data['id'] as $key =>$value){
                if($value['id']==$depart_id){
                    $params = $value;
                }
                if($value['pid']==$depart_id){
                    $child[$key] = $value;
                }
            }
        }


        $params['child']=array();
        if(is_array($child)){//child
            foreach($child as $key=>$value){
                if(isset($value['id'])){
                    $params['child'][$value['id']] =  $this->applyTree($value['id'],$data);
                }

            }

        }else{
            $params['child']='';
        }
        return $params;

    }



}