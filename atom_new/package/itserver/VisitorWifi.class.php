<?php
namespace Atom\Package\Itserver;
use Atom\Package\Common\BaseQuery;
/**
 * speed本地登记访客wifi账号
 * @author hongzhou
 * @date 2015-9-2
 */
class VisitorWifi extends BaseQuery{
	

	private static $col = array('wifi_id', 'id', 'user_id', 'visitor_name', 'interview', 'visitor_mobile', 'company', 'reason','create_time', 'expire_time', 'channel', 'status', 'handle_status');
    /**
     * @return string
     */
    public static function database()
    {
        return 'administration';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'visitor_wifi';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'wifi_id';
    }
	/**
	 * 所有字段及默认值
	 */
	private static $fields = array(
        'wifi_id'        => 0 ,
		'id'             => 0 ,
		'user_id'        => 0 ,
		'visitor_name'   => '' ,
		'interview'      => '' ,
		'visitor_mobile' => '' ,
		'company'        => '' ,
		'reason'         => '' ,
		'create_time'    => '0000-00-00' ,
		'expire_time'    => '0000-00-00' ,
		'channel'        => 0 ,
		'status'         => 1 ,
        'handle_status'    => 0 ,//0待审批,1审批,2驳回
	);

    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * 获取信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 10)
    {
        if ( empty($params['status'])) {
            $params['status'] =array(0,1);
        }
   
        if (!is_array($params)) {
            return FALSE;
        }
        //查询
        $builder = $this->builder();
        $builder->select(static::$col);

        //user_id
        if (!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            }else{
                $builder->where('user_id', '=', $params['user_id']);
            }
        }
        if (!empty($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            }else{
                $builder->where('id', '=', $params['id']);
            }
        }
        if (!empty($params['wifi_id'])) {
            if (is_array($params['wifi_id'])) {
                $builder->whereIn('wifi_id', $params['wifi_id']);
            }else{
                $builder->where('wifi_id', '=', $params['wifi_id']);
            }
        }
        if (!empty($params['interview'])) {
            if (is_array($params['interview'])) {
                $builder->whereIn('interview', $params['interview']);
            }else{
                $builder->where('interview', '=', $params['interview']);
            }
        }

        if (!empty($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            }else{
                $builder->where('status', '=', $params['status']);
            }
        }
        $builder->orderBy('wifi_id','desc');
        if(isset($param['count'])&& $param['count']== 1){
            return $builder->hash('wifi_id')->get();
        }
        $offset = $offset < 1 ? 1: $offset;
       return $builder->hash('wifi_id')->offset(($offset-1)*$limit)->limit($limit)->get();
    }
    /**
     * 更新
     */
    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk])&& $params[$pk] > 0){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk,$id)->update($params);
        }
        if(isset($params['id'])  && $params['id'] > 0) {
            $id = intval($params['id']);
            unset($params['id']);
            return $this->builder()
                ->where('id',$id)->update($params);
        }
        return FALSE;
    }
    /**
     * 添加
     */
    public function insert($params) {

        if (empty($params['id']) ||empty($params['visitor_name'])||empty($params['visitor_mobile'])||empty($params['interview'])){
            return FALSE;
        }
        $params = array_intersect_key($params, self::$fields);
        $params = array_merge(self::$fields, $params);
        unset($params[static::pk()]);

        return $this->builder()->insert($params);

    }


}
