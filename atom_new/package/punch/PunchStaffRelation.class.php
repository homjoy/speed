<?php
namespace Atom\Package\Punch;

use Atom\Package\Common\BaseQuery;

/**
 * 门禁卡 和用户 user_id staff_id 同步数据库
 * Class CreatePunchLog
 * @package Atom\Package\Punch
 * @author guojiezhu@meilishuo.com
 * @since 2015-10-19
 */
class PunchStaffRelation extends BaseQuery
{
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
    public static function tableName() {
        return 'punch_staff_relation';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'user_id';
    }

    public static $col = array(
            'user_id',
            'staff_id',
            'punch_staff_id',
            'name_cn',
            'status',
            'update_time'

        );

    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
            'user_id'     => 0,
            'staff_id'    => '',
            'punch_staff_id'   => '',
            'name_cn' => '',
            'status'  => '',
            'update_time' => '',
        );

    public static function getFields()
    {
        return self::$fields;
    }


    /**
     * 保存关系
     *
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function savePunchStaffRelation($data)
    {
        if (empty($data)) {
            return '';
        }
        try {
            $builder = $this->builder();
            $punchLogIds = $builder->insert($data);
            return $punchLogIds;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询门禁卡卡号是否已经存在
     *
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {
        if (!is_array($params)) {
            return FALSE;
        }
        //状态：默认有效
        if (!isset($params['status'])) {
            $params['status'] = array(0);
        }
        //查询
        $builder = $this->builder();
        $builder->select(static::$col);
        //状态
        if(!empty($params['status']) ) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            } else {
                $builder->where('status', '=', $params['status']);
            }
        }
        if(!empty($params['punch_staff_id'])) {
            if (is_array($params['punch_staff_id'])) {
                $builder->whereIn('punch_staff_id', $params['punch_staff_id']);
            } else {
                $builder->where('punch_staff_id', '=', $params['punch_staff_id']);
            }
        }
        if(!empty($params['user_id'])) {
            if (is_array($params['user_id'])) {
                $builder->whereIn('user_id', $params['user_id']);
            } else {
                $builder->where('user_id', '=', $params['user_id']);
            }
        }
        $builder->orderBy(static::pk(), 'asc')->get();

        //是否获取全部
        if (isset($params['all']) && $params['all'] == 1) {
            return $builder->hash(static::pk())->get();
        }
        if (isset($params['count']) && $params['count'] == 1) {
            return $builder->count();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    /**
     * 更新
     */
    public function  updateById($params){
        $pk = static::pk();
        if(isset($params[$pk]) && !is_array($params[$pk])){
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()->where($pk,$id)->update($params);
        }
        return FALSE;
    }
}