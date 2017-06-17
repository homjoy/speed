<?php
namespace Atom\Modules\Department;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Account\DepartmentInfo;
use Libs\Util\ArrayUtilities;
use Atom\Package\Common\BaseMemcache;

/**
 * 获取部门信息 或 批量获取
 * @author hongzhou@meilishuo.com
 * @date 2015-8-17 上午10:56:25
 */

class DepartInfoTree extends \Atom\Modules\Common\BaseModule{

    protected $params = array();
    protected $errors = array();
    private static $expireTime = 600;
    private static $ceo = array (
        'depart_id' => '1',
        'depart_name' => 'CEO',
        'depart_info' => 'CEO',
        'depart_level' => '0',
        'parent_id' => '0',
        'child_id' => '0',
        'memo' => '',
        'update_time' => '2015-10-21 15:01:52',
        'is_official' => '1',
        'is_virtual' => '0',
        'level' => '0',
        'status' => '1',
    );

    public function run() {

        $this->_init();
        if($this->query()->hasError()){
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        $queryParams = array();
        if(($this->params['interval']!='') && !empty($this->params['update_time'])){
            $queryParams['end_time'] = strtotime($this->params['update_time'])+$this->params['interval'];
            $queryParams['end_time'] = @date('Y-m-d H:i:s',$queryParams['end_time']);
        }
        if (!empty($this->params['update_time'])) {
            $queryParams['update_time']=@date('Y-m-d H:i:s',strtotime($this->params['update_time']));
        }
        if ($this->params['depart_id']!='') {
            $queryParams['depart_id'] = $this->params['depart_id'];
        }

        if ($this->params['parent_id']!=''){
            $queryParams['parent_id'] = $this->params['parent_id'];
        }

        if ($this->params['depart_name']!=''){
            $this->params['depart_name'] = trim($this->params['depart_name'] );
            $queryParams['depart_name'] = $this->params['depart_name'];
        }
        if ($this->params['depart_level']!='') {
            $queryParams['depart_level'] = $this->params['depart_level'];
        }
        if ($this->params['status']!='') {
            $queryParams['status'] = $this->params['status'];
        }
        if(empty($queryParams)){//以上为过滤条件 不要修改这个判断的地方
            $return = Response::gen_error(10001, '', $this->query()->getErrors());
            return $this->app->response->setBody($return);
        }
        if ($this->params['count']!=''){
            $queryParams['count'] = $this->params['count'];
        }
        if ($this->params['match']!='') {
            $queryParams['match'] = $this->params['match'];
        }
        if ($this->params['all']!=''){
            $queryParams['all'] = $this->params['all'];
        }
        if ($this->params['display_level']!=''){
            $queryParams['display_level'] = $this->params['display_level'];
        }

        $ret = self::getTreeDeparts($queryParams, $this->params['offset'], $this->params['limit']);
        if ($ret === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success($ret);
        }

        $this->app->response->setBody($return);
        
   }

   //获取部门树
   public static function getTreeDeparts($params = array())
   {
        //缓存
        $cacheKey = 'Atom:Department:getTreeDeparts:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        if (!isset($params['depart_id'])) {
            return self::getAllDepartTree();
        }

        $departIds = $params['depart_id'];
        $params['depart_id'] = $departIds = array_unique($departIds);

        $return = array();
        foreach ($departIds as $v) {
            $return[$v] = self::getPerDepartTree($params, $v);
        }
        return $return;
    }

   //获取并生成部门树
   private static function getPerDepartTree($params = array(), $depart_id = 0)
   {
        $display_level = isset($params['display_level']) ? intval($params['display_level']) : 0;

        //缓存
        $cacheKey = 'Atom:Department:getPerDepartTree:'.$depart_id.':'.$display_level;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //获取所有部门
        $allDeparts = self::getAllDepartTree();

        if (isset($allDeparts[$depart_id])) {
            $return = $allDeparts[$depart_id];
        }else{
            $return = array();
        }

        //如果没有出错，则生成缓存
        $rebuild = self::rebuildAndFix($return);
        if (!$rebuild) {
            BaseMemcache::instance()->set($cacheKey, $return, self::$expireTime);
        }

        return $return;
   }

   //获取所有部门树
   private static function getAllDepartTree($offset = 0, $limit = 99999, $refresh = 0)
   {
        //缓存
        $cacheKey = 'Atom:Department:getAllDepart';
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData) && $refresh === 0) {
            return $cacheData;
        }

        //查询
        $params['depart_level'] = array(1,2,3,4,5,6,7,8,9);
        $ret = DepartmentInfo::model()->getDataList($params, $offset, $limit);
        if ($ret == FALSE) {
            return FALSE;
        }

        $data = ArrayUtilities::buildSupTree($ret, 'parent_id', 'depart_id');
        $data[1] = self::$ceo;

        BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);
        return $data;
   }

   //检测并修复数据
   private static function rebuildAndFix($depart = array()){
        if (empty($depart)) {
            array();
        }

        //如果数据不对则刷新数据
        if (isset($depart['parent_id']) && !empty($depart['parent_id'])) {
            if (!isset($depart['parents']) || empty($depart['parents'])) {
                self::getAllDepartTree(0, 9999, 1);
                return TRUE;
            }
        }
        return FALSE;
   }

   //获取参数
    private function _init() {
        $this->rules = array(//查询
            'depart_id'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
            'depart_name'=>array(
                'type'=>'string',
            ),
            'depart_level'=>array(
                'type'=>'multiId',
                'default'=>array(1,2,3,4,5,6,7,8,9),
            ),
            'parent_id'=>array(
                'type'=>'multiId',
                'default'=>''
            ),
            'status'=>array(
                'type'=>'multiId',
                'default'=>'',
            ),
            'update_time'=>array(
                'type'=>'string',
            ),
            'offset'=>array(
                'type'=>'integer',
                'default'=>0,
            ),
             'limit'=>array(
               'type'=>'integer',
                 'default'=>100,
           ),
            'interval'=>array(
                'type'=>'integer',
            ),
            'match'=>array(
                'type'=>'string',
                 'default'=>'like'
            ),
            'count' => array(   //当为1时获取总条数
                'type' => 'integer',
            ),
            'all' => array(   //当为1时获取所有数据
                'type' => 'integer',
                'default'=>1,
            ),
            'display_level'=>array(
                'type'=>'integer',
                'default'=>3,
            ),
        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }

}
