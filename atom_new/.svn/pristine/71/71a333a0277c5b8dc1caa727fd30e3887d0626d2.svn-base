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

class HackedDepartInfoList extends \Atom\Modules\Common\BaseModule{

    protected $params = array();
    protected $errors = array();
    private static $expireTime = 600;
    private static $ceo = array (
        'depart_id' => '1',
        'depart_name' => '总裁办公室',
        'depart_info' => '总裁办公室',
        'depart_level' => '0',
        'parent_id' => '0',
        'child_id' => '0',
        'memo' => '',
        'update_time' => '2015-10-21 15:01:52',
        'is_official' => '1',
        'is_virtual' => '0',
        'level' => '0',
        'status' => '1',
        'parent_ids' => array(),
        'child_ids' => array(),
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

        $ret = self::getData($queryParams, $this->params['offset'], $this->params['limit']);
        if ($ret === FALSE) {
            $return = Response::gen_error(50001);
        }else{
            $return = Response::gen_success($ret);
        }

        $this->app->response->setBody($return);
   }

   //获取部门树
   public static function getData($params = array())
   {
        //缓存
        $cacheKey = 'Atom:HackedDepartment:getData:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //departs
        $allDeparts = self::getTreeDeparts($params);

        if (!isset($params['depart_id'])) {
            return array();
            //return $allDeparts;
        }

        $departIds = $params['depart_id'];
        $params['depart_id'] = $departIds = array_unique($departIds);

        $return = array();
        foreach ($departIds as $id) {
            if (isset($allDeparts[$id])) {
                $return[$id] = $allDeparts[$id];
            }
        }

        BaseMemcache::instance()->set($cacheKey, $return, self::$expireTime);
        return $return;
    }

   //获取部门树
   public static function getTreeDeparts($params = array())
   {
        unset($params['depart_id']);
        //缓存
        $cacheKey = 'Atom:HackedDepartment:getTreeDeparts:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //parents
        $allDeparts = self::getAllDepartTree();

        $return = array(1=>self::$ceo,);
        foreach ($allDeparts as $k => $v) {
            $return[$k] = self::getPerDepart($params, $allDeparts[$k], $k);
        }

        //child_ids
        foreach ($return as $k => $v) {
            $return[$k]['child_ids'] = array();
            if (!empty($v['parent_ids'])) {
                foreach ($v['parent_ids'] as $pid) {
                    if (!isset($return[$pid]['child_ids'])) {
                        $return[$pid]['child_ids'] = array();
                    }
                    array_push($return[$pid]['child_ids'], $k);
                }
            }
        }

        BaseMemcache::instance()->set($cacheKey, $return, self::$expireTime);
        return $return;
    }

   //获取所有部门树
   private static function getAllDepartTree($offset = 0, $limit = 99999)
   {
        //缓存
        $cacheKey = 'Atom:HackedDepartment:getAllDepartSupTree';
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $ret = self::getAllDepart($offset, $limit);

        $data = ArrayUtilities::buildSupTree($ret, 'parent_id', 'depart_id');
        BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);
        return $data;
   }

   //获取所有部门树
   private static function getAllDepart($offset = 0, $limit = 99999)
   {
        //缓存
        $cacheKey = 'Atom:HackedDepartment:getAllDepart';
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $params['depart_level'] = array(1,2,3,4,5,6,7,8,9);
        $data = DepartmentInfo::model()->getDataList($params, $offset, $limit);
        if ($data == FALSE) {
            return FALSE;
        }

        BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);
        return $data;
   }

   //获取并生成部门树
   private static function getPerDepart($params = array(), $depart = array(), $depart_id = 0)
   {
        $display_level = isset($params['display_level']) ? intval($params['display_level']) : 0;

        //缓存
        $cacheKey = 'Atom:HackedDepartment:getPerDepart:'.$depart_id.':'.$display_level;
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        if (empty($depart)) {
            return FALSE;
        }

        $item = $depart;
        if (isset($item['parents'])) {
            unset($item['parents']);
        }

        $tree = ArrayUtilities::arrangeTree($depart, 'parents');
        $i = 0;
        $item['depart_name'] = '';
        $item['parent_ids'] = array();
        foreach ($tree as $t => $r) {
            if ($i++ < $display_level || $display_level == 0) {
                $item['depart_name'] .= $r['depart_name'];
                $item['depart_name'] .= '-';
                $item['parent_ids'][] = $r['depart_id'];
            }
        }
        $item['depart_name'] = rtrim($item['depart_name'], '-');

        BaseMemcache::instance()->set($cacheKey, $item, self::$expireTime);
        return $item;
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
