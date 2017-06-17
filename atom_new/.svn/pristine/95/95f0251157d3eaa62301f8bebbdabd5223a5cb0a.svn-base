<?php
namespace Atom\Package\Migrate;
use Atom\Package\Common\BaseQuery;

/**
 * Class Crab
 * @package Atom\Package\Migrate
 */
class Crab extends BaseQuery{
    /**
     * crab 库.
     * @return string
     */
    public static function database()
    {
        return 'office';
    }

    /**
     * 查询CRAB表的名片部门列表
     * @return null|\stdClass
     */
    public function getPunchAllDeparts(){
        $builder = $this->builder('t_crab_punch_approve');
        $builder->where('value_type', '=', 0);
        $result = $builder->get();
        $depts = array();
        if(!empty($result)){
            foreach($result as $val){
                $uid = $val['value_id'];
                $depts[$uid] = 1;
            }
        }
        return $depts;
    }

    /**
     * 查询CRAB表的名片人员列表
     * @return null|\stdClass
     */
    public function getPunchAllUsers(){
        $builder = $this->builder('t_crab_punch_approve');
        $builder->where('value_type', '=', 1);
        $result = $builder->get();
        $depts = array();
        if(!empty($result)){
            foreach($result as $val){
                $uid = $val['value_id'];
                $depts[$uid] = 1;
            }
        }
        return $depts;
    }

    /**
     * 查询CRAB表的部门列表
     * @param array $params
     * @return null|\stdClass
     */
    public function getDepartByIds($params){
        $builder = $this->builder('t_crab_staff_depart'); //带上查询的表即可
        if (!empty($params['depart_id'])) {
            if (is_array($params['depart_id'])) {
                $builder->whereIn('departid', $params['depart_id']);
            }else{
                $builder->where('departid', '=', $params['depart_id']);
            }
        }
        return $builder->get();
    }

    /**
     * 新增CRAB表的部门列表
     * @param array $params
     * @return null|\stdClass
     */
    public function insertDepart($params){
        $builder = $this->builder('t_crab_staff_depart'); //带上查询的表即可
        if (empty($params['departid']) || empty($params['departheader'])){
            return FALSE;
        }
        return $builder->onDuplicateKeyUpdate($params)->insert($params);
    }

    /**
     * 修改CRAB表的部门列表
     * @param array $params
     * @return null|\stdClass
     */
    public function updateDepart($params){
        $builder = $this->builder('t_crab_staff_depart'); //带上查询的表即可
        $pk = 'departid';
        $id = intval($params[$pk]);
        unset($params[$pk]);
        return $builder->where($pk,$id)->update($params);
    }
    /**
     * 查询CRAB表的用户列表
     * @param array $params
     * @return null|\stdClass
     */
    public function getStaffInfoByIds($params =array()){
        $builder = $this->builder('t_crab_staff_info'); //带上查询的表即可
        //print_r($params);die;
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('sid', $params['user_id']);
            }else{
                $builder->where('sid', '=', $params['user_id']);
            }
        }
        if (!empty($params['mail'])){
            if (is_array($params['mail'])) {
                $builder->whereIn('mail', $params['mail']);
            }else{
                $builder->where('mail', '=', $params['mail']);
            }
        }
        if (!empty($params['direct_leader'])){
            if (is_array($params['direct_leader'])) {
                $builder->whereIn('direct_leader', $params['direct_leader']);
            }else{
                $builder->where('direct_leader', '=', $params['direct_leader']);
            }
        }
        if (isset($params['update_time'])  && isset($params['end_time'])){
            $builder->whereBetween('ctime',$params['update_time'],$params['end_time']);
        }
        if(!empty($params['status'])){
            $builder->whereIn('status',$params['status']);
        }else{
            $builder->where('status','=',1);
        }

        return $builder->get();
    }

    /**
     * 查询CRAB表的会议室列表
     * @param int $offset
     * @param int $limit
     * @return null|\stdClass
     */
    public function getMeetingRooms($offset = 0,$limit = 100)
    {
        $builder = $this->builder('t_crab_meeting_room'); //带上查询的表即可
        return $builder->offset($offset)->limit($limit)->get();
    }

    /**
     * 分段查询会议室预定
     * @param $offset
     * @param $limit
     * @return null|\stdClass
     */
    public function getMeetingBooks($offset,$limit)
    {
        $builder = $this->builder('t_crab_meeting_book');
        return $builder->offset($offset)->limit($limit)->get();
    }

    /**
     * 获取预定总数
     * @return int
     */
    public function booksCount()
    {
        $builder = $this->builder('t_crab_meeting_book');
        return $builder->count();
    }

    /**
     * 查询主会场.
     * @param $relationBookId
     * @return null|\stdClass
     */
    public function getMainBook($relationBookId)
    {
        $builder = $this->builder('t_crab_meeting_book_relation');
        $builder->where('relation_book_id',$relationBookId);
        return $builder->first();
    }

    /**
     * 分段查询预定分会场信息
     * @param $offset
     * @param $limit
     * @return null|\stdClass
     */
    public function relationBook($offset,$limit)
    {
        $builder = $this->builder('t_crab_meeting_book_relation');
        return $builder->offset($offset)->limit($limit)->get();
    }

    /**
     * 统计有分会场的预定数量
     * @return int
     */
    public function relationBookCount()
    {
        $builder = $this->builder('t_crab_meeting_book_relation');
        return $builder->count();
    }

    /**
     * 拒绝记录.
     * @return null|\stdClass
     */
    public function cancelBooks()
    {
        $builder = $this->builder('t_crab_meeting_cancel');
        return $builder->get();
    }

    /**
     * 查询预定的参与用户.
     * @param $bookId
     * @return null|\stdClass
     */
    public function bookHasUsers($bookId)
    {

        $builder = $this->builder('t_crab_meeting_book_user');
        $builder->where('book_id',$bookId);
        return $builder->get();
    }


    /**
     * 获取有更新的头像信息
     * @param $startTime
     * @return null|\stdClass
     */
    public function getUpdatedStaffAvatar($startTime)
    {
        $builder = $this->builder('t_crab_staff_extinfo');
        $builder->where('update_time','>=',$startTime);
        return $builder->get();
    }

    /**
     * 获取更新的部门信息.
     * @param $startTime
     * @return null|\stdClass
     */
    public function getUpdatedDepart($startTime)
    {
        $builder = $this->builder('t_crab_staff_depart');
        $builder->where('update_time','>=',$startTime);
        return $builder->get();
    }

    /**
     * 需要更新的员工信息
     * @param $startTime
     * @return null|\stdClass
     */
    public function getUpdatedStaffInfo($startTime)
    {
        $builder = $this->builder('t_crab_staff_info');
        $builder->where('ctime','>=',$startTime);
        return $builder->get();
    }

    /**
     * 查询共享人信息
     * @param $params
     * @return null|\stdClass
     */
    public function getTimeShare(array $params = array()){
        $builder = $this->builder('t_crab_staff_shareinfo');

        //查询条件
        if(!empty($params)){
            foreach($params as $k => $v){
                if(is_array($v)){
                    $builder->whereIn($k, $v);
                }else{
                    $builder->where($k,$v);
                }
            }
        }
        return $builder->get();
    }

    /**
     * 查询头像信息
     * @param $params
     * @return null|\stdClass
     */
    public function getStaffAvatarByUserId($user_id){
        $builder = $this->builder('t_crab_staff_extinfo');

        $builder->where('user_id',$user_id);
        return $builder->get();
    }

    /**
     *
     *  获取请假信息
     * @author haibinzhou
     * @date 2015-08-11
     *
     */
    public function getLeaveInfo(){

        $builder = $this->builder('t_crab_document')->where('doc_type','1');

        // $builder = $this->builder('t_crab_document')->where('doc_type','1')->offset($offset)->limit($limit);
        return $builder->get();
    }

    /**
     *
     *  获取固定资产信息
     * @author minggeng
     * @date 2015-08-11
     *
     */
    public function getAssetsInfo(){

        $builder = $this->builder('t_crab_document')->where('doc_type','14');
        $builder->whereIn('doc_status',array(0,1));
        //$builder->where('doc_id',31537);
        return $builder->get();
    }

    /**
     *
     *  获取名片信息
     * @author gengming
     * @date 2015-11-16
     *
     */
    public function getDocumentInfo($params){
        $builder = $this->builder('t_crab_document');
        if(isset($params['doc_status'])){
            $builder->whereIn('doc_status',$params['doc_status']);
        }
        if(isset($params['doc_type'])){
            $builder->where('doc_type',$params['doc_type']);
        }
        if(isset($params['user_id'])){
            $builder->where('user_id',$params['user_id']);
        }

        return $builder->get();
    }

    /**
     *
     *  获取审批信息
     * @author gengming
     * @date 2015-11-16
     *
     */
    public function getDocumentProcessInfo($params){

        $builder = $this->builder('t_crab_document_process');
        if(isset($params['doc_id'])){
            $builder->where('doc_id',$params['doc_id']);
        }
        $builder->whereNotIn('doc_change_to_status',array(0,4));

        return $builder->get();
    }

    /**
     *
     *  获取请假信息
     * @author haibinzhou
     * @date 2015-08-11
     *
     */
    public function getLeave(){

        $builder = $this->builder('t_crab_absence_record');

        // $builder = $this->builder('t_crab_document')->where('doc_type','1')->offset($offset)->limit($limit);
        return $builder->get();
    }


    /**
     * @param $uid
     * @return null|\stdClass
     * 根据doc_id 获取老的请假状态更新新表
     * @author haibinzhou@meilishuo.com
     * @date 2015-09-30
     *
     */
    public function getApproveStatus($params = array()){
        $builder = $this->builder('t_crab_document')->where('doc_type','1')->whereIn('doc_id',$params);
        return $builder->get();
    }


    /*
     *
     *  获取老数据库的用户头像
     *  @author haibinzhou
     *  @date 2015-08-20
     */

    public function getUserAvatar($uid){
        $builder = $this->builder('t_crab_staff_extinfo');
        return $builder->where('user_id',$uid)->get();
    }

    /*
     *
     *  更新老数据库的头像信息
     *  @author haibinzhou
     *  @date 2015-08-20
     */

    public function updateAvatar($params){
        $builder = $this->builder('t_crab_staff_extinfo');
        return $builder->where('user_id',$params['user_id'])->update($params);
    }
    /*
  *  更新
  *  @author hongzhou
  *  @date 2015-11-10
  */
    public function updateStaff($params){
        $builder = $this->builder('t_crab_staff_info');
        return $builder->where('sid',$params['sid'])->update($params);
    }
    /*
    *  添加
    *  @author hongzhou
    *  @date 2015-11-10
    */
    public function insertStaff($params){
        $builder = $this->builder('t_crab_staff_info');
        return $builder->insert($params);
    }
    /*
     *  插入头像
     *  @author haibinzhou
     *  @date 2015-08-20
     */

    public function insertAvatar($params){
        $builder = $this->builder('t_crab_staff_extinfo');
        return $builder->insert($params);
    }

    /*
     *  修改员工手机号
     *  @author haibinzhou@meilishuo.com
     *  @date 2015-11-09
     */
    public function updateUserPhone($params){
        $builder = $this->builder('t_crab_staff_info');
        return $builder->where('sid',$params['sid'])->update($params);
    }

}
