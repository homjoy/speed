<?php

namespace Admin\Modules\Structure\Depart;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Department\Department;
/**
 *  DepartHomeBackup
 * hongzhou@meilishuo.com
 * 2015-09-25
 */
class DepartHomeBackup extends BaseModule {
    protected $all = 1;
    protected $status = 1;
    public static $ROOT_DEPART=0;
    public static $ROOT_DEPART_LEVEL=0;
    public function run() {
        $depart =array();
        $depart = Department::getInstance()->getDepartTemp(
            array(
                'all'=>$this->all,
                'status'=>$this->status
            )
        );
        $data = array('depart'=>$depart);
        $d_tree =array();
        if(is_array($depart)){
            foreach($depart as $key=>$value){
                if($value['parent_id']==self::$ROOT_DEPART){
                    $d_tree[]= $this->departTree($value['depart_id'],$data);
                }

            }
        }
        $p =array(//顶级节点
            "depart_id" =>self::$ROOT_DEPART,
            "depart_name"=>'美丽说',
            "depart_info"=>'美丽说',
            "depart_level"=>self::$ROOT_DEPART_LEVEL,
            "parent_id"=>'',
            "memo"=>'美丽说',
            'child'=>is_array($d_tree)?$d_tree:'',
        );
        if(empty($p)){
            return $this->app->response->setBody(Response::gen_success(array()));
        }
        $this->app->response->setBody(Response::gen_success(array($p)));
    }

    private  function departTree($depart_id,$data){//depart_id


        $params= $child =  array();
        if(is_array($data['depart'])){
            foreach($data['depart'] as $key =>$value){
                if($value['depart_id']==$depart_id){
                    $params = $value;
                }
                if($value['parent_id']==$depart_id){
                    $child[$key] = $value;
                }
            }
        }


        $params['child']=array();
        if(is_array($child)){//child
            foreach($child as $key=>$value){
                if(isset($value['depart_id'])){
                    $params['child'][$value['depart_id']] =  $this->departTree($value['depart_id'],$data);
                }

            }

        }else{
            $params['child']='';
        }
        return $params;

    }




}