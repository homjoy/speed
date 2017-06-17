<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Migrate\Crab;
/**
 * 同步老SPEED 员工固定资产信息
 * Class SyncOldSpeedAssets
 * @package Atom\Scripts\Sync
 */
class SyncOldSpeedAssets extends \Frame\Script{
    public function run(){

        //同步固定资产信息.
        $this->getInfo(0,20000);

        return $this->response->setBody("同步结束.\n");
    }

    private function getInfo(){
        $data = Crab::model()->getAssetsInfo();

        $result = array();
        foreach($data as $value){
            $others = json_decode($value['doc_data_struct'],true);
            $n = count($others['plan']['assetsname']);
            $params = array();
            for($i=0;$i<$n;$i++){
                foreach($others['plan'] as $k => $v){
                    if($k == 'company_name'||$k == 'company_name1'||$k == 'company_name2'){
                        $p = array();
                        $p['order_id'] = $value['doc_id'];
                        //$p['name']  = $others['name'];
                        //$p['assetsname'] = $others['plan']['assetsname'][0];
                        $p['company_name'] = $v[$i];
                    }
                    if($k == 'equipment'||$k == 'equipment1'||$k == 'equipment2'){
                        $p['equipment'] = $v[$i];
                    }
                    if($k=='brand'||$k=='brand1'||$k=='brand2'){
                        $p['brand'] = $v[$i];
                    }
                    if($k=='model'||$k=='model1'||$k=='model2'){
                        $p['model'] = $v[$i];
                    }
                    if($k=='price'||$k=='price1'||$k=='price2'){
                        $p['price'] = 'jiage';//$v[0];
                        $params[] = $p;
                    }
                }
            }
            $result[] = $params;
        }
        foreach($result as $r){
            foreach($r as $d){
                $str = join('|',$d);
                var_dump($str);
            }
        }
        die();
    }
}
