<?php
namespace Apicloud\Package\Department;

use Apicloud\Package\Common\BasePackage;
use Libs\Util\ArrayUtilities;
use Apicloud\Package\Common\BaseMemcache;
/**
 * 部门
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-15
 */
class DepartmentRelation extends BasePackage {

	private static $instance = NULL;
	private static $expireTime = 300;  //5分钟
	
	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    public static function get($params) {
        $cacheKey = MEM_PREFIX.'Department:DepartRelation:';
        $cacheKey .= ArrayUtilities::genParamsCacheKey($params);
        $cacheData = BaseMemcache::instance()->get($cacheKey);
        if (!empty($cacheData)) {
            return $cacheData;
        }

        //查询
        $result = self::getClient()->call('atom', 'department/dept_relation_list', $params);


        if($result['httpcode'] != 200) {
            return FALSE;
        } elseif(empty($result['content']) || $result['content']['error_code'] != 0) {
            return FALSE;
        }else{
            $data = $result['content']['data'];
            //生成缓存
            BaseMemcache::instance()->set($cacheKey, $data, self::$expireTime);

            return $data;
        }
    }
    //添加
	public static  function create($params = array()) {
		if(!is_array($params)) {
			return FALSE;
		}
	
		$result = self::getClient()->call('atom', 'department/create_dept_relation', $params);

		if($result['httpcode'] != 200 || empty($result['content'])) {
			return FALSE;
		}else {
			return $result['content'];
		}
	}

	/**
	 * 修改
	 * @param unknown $params
	 * @return boolean|array
	 */
	public static function update($params = array()) {

		if(!is_array($params) || empty($params['depart_id'])) {
			return FALSE;
		}
		
		$result = self::getClient()->call('atom', 'department/update_dept_relation', $params);
		if($result['httpcode'] != 200 || empty($result['content'])) {
			return FALSE;
		}else {

			return $result['content'];

		}
	}





}