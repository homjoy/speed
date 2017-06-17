<?php

namespace Atom\Scripts\Sync;

use Atom\Package\Account\DepartmentInfo;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Migrate\Crab;
use Libs\Util\ArrayUtilities;
/**
 * 同步新SPEED 部门信息
 * Class SyncNewSpeedDepartmentInfo
 * @package Atom\Scripts\Sync
 */
class SyncNewSpeedDepartmentInfo extends \Frame\Script {

    /**
     * 同步的间隔数据,五分钟同步一次.
     * @var int
     */
    protected $interval = 300;
    private $update_department = array();

    public function run(){

        $now = time();

        $startTime = date('Y-m-d H:i:s', $now - $this->interval);

        //department_info修改
        $this->syncUpdatedDepart($startTime);

        //department_relation修改
        $this->syncUpdatedDepartRelation($startTime);

        //department_sub修改
        $this->syncUpdatedDepartSub($startTime);

        return $this->response->setBody("同步结束.\n");
    }

    /**
     * 同步部门信息.
     * @param $startTime
     */
    protected function syncUpdatedDepart($startTime) {
        //获取new—speed最近5分钟修改数据
        $p = array(
            'all'       => 1,
            'update_time'=> $startTime,
        );
        $departments = DepartmentInfo::model()->getDataList($p);
        if (empty($departments)) {
            return;
        }
        $depart_ids = ArrayUtilities::my_array_column($departments,'depart_id');

        //获取部门负责人
        $p = array(
            'all'       => 1,
            'depart_id' => $depart_ids,
            'is_virtual'=> 0,
        );
        $depart_relation = DepartmentRelation::model()->getDataList($p);
        if(empty($depart_relation)){
            return;
        }
        $depart_relation = ArrayUtilities::hashByKey($depart_relation,'depart_id');
        foreach($depart_relation as &$d_r){
            //如果部门负责人为0，去sub表去代理人
            if($d_r['user_id'] == 0){
                $p = array(
                    'relation_id' => $d_r['relation_id'],
                );
                $depart_sub = DepartmentSub::model()->getDataList($p);
                if(empty($depart_sub)){
                    continue;
                }
                //如果部门有多个代理会有问题
                $depart_sub = ArrayUtilities::hashByKey($depart_sub,'relation_id');
                $depart_sub = array_pop($depart_sub);
                $d_r['user_id'] = $depart_sub['user_id'];
            }
        }

        foreach ($departments as $depart) {
            //记录修改过次部门，后续不再重新同步
            $this->update_department[$depart['depart_id']] = $depart['depart_id'];

            $oldDepart = array();
            $oldDepart['departid']      = $depart['depart_id'];
            $oldDepart['departname']    = $depart['depart_name'];   //部门name
            $oldDepart['departinfo']    = $depart['depart_info'];   //部门信息
            $oldDepart['father']        = $depart['parent_id'];     //上级部门id

            if(isset($depart_relation[$depart['depart_id']])){
                $oldDepart['departheader']  = $depart_relation[$depart['depart_id']]['user_id'];//部门leader
            }

            $p = array(
                'depart_id' => $depart['depart_id'],
            );
            $exist = Crab::model()->getDepartByIds($p);

            if (empty($exist)) {
                Crab::model()->insertDepart($oldDepart);
            } else {
                Crab::model()->updateDepart($oldDepart);
            }
        }
    }

    /**
     * 同步部门relation信息.
     * @param $startTime
     */
    protected function syncUpdatedDepartRelation($startTime){
        //获取new—speed最近5分钟修改数据
        $p = array(
            'all'       => 1,
            'update_time'=> $startTime,
        );
        $depart_relation = DepartmentRelation::model()->getDataList($p);
        if (empty($depart_relation)) {
            return;
        }
        $depart_relation = ArrayUtilities::hashByKey($depart_relation,'depart_id');
        foreach($depart_relation as $key => &$d_r){
            if(in_array($d_r['depart_id'],$this->update_department)){
                unset($depart_relation[$key]);
                continue;
            }
            //如果部门负责人为0，去sub表去代理人
            if($d_r['user_id'] == 0){
                $p = array(
                    'relation_id' => $d_r['relation_id'],
                );
                $depart_sub = DepartmentSub::model()->getDataList($p);
                if(empty($depart_sub)){
                    continue;
                }
                //如果部门有多个代理会有问题
                $depart_sub = ArrayUtilities::hashByKey($depart_sub,'relation_id');
                $depart_sub = array_pop($depart_sub);
                $d_r['user_id'] = $depart_sub['user_id'];
            }
        }

        foreach ($depart_relation as $d_r) {
            //记录修改过次部门，后续不再重新同步
            $this->update_department[$d_r['depart_id']] = $d_r['depart_id'];

            $oldDepart = array();
            $oldDepart['departid']      = $d_r['depart_id'];
            $oldDepart['departheader']  = $depart_relation[$d_r['depart_id']]['user_id'];//部门leader

            Crab::model()->updateDepart($oldDepart);
        }
    }

    /**
     * 同步部门sub信息.
     * @param $startTime
     */
    protected function syncUpdatedDepartSub($startTime){
        //获取new—speed最近5分钟修改数据
        $p = array(
            'all'       => 1,
            'update_time'=> $startTime,
        );
        $depart_sub = DepartmentSub::model()->getDataList($p);
        if (empty($depart_sub)) {
            return;
        }

        $depart_sub = ArrayUtilities::hashByKey($depart_sub,'relation_id');

        foreach($depart_sub as $key => $d_r){
            $p = array(
                'relation_id'=> $d_r['relation_id'],
            );
            $depart_relation = DepartmentRelation::model()->getDataList($p);
            $depart_relation = array_pop($depart_relation);

            if(in_array($depart_relation['depart_id'],$this->update_department)){
                continue;
            }

            $oldDepart = array();
            $oldDepart['departid']      = $depart_relation['depart_id'];
            $oldDepart['departheader']  = $d_r['user_id'];//部门leader

            Crab::model()->updateDepart($oldDepart);
        }
    }
}
