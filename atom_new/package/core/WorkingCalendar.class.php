<?php
namespace Atom\Package\Core;

use Atom\Package\Common\BaseQuery;

/**
 * Class Config
 * @package Atom\Package\core
 */
class WorkingCalendar extends BaseQuery
{

    private static $pk = 'id';

    private static $sample = array(
        'id' => 0,
        'date' => '0000-00-00',
        'type' => '1',
        'title' => '',
        'create_time' => '0000-00-00 00:00:00',
        'status' => 1,
    );

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName()
    {
        return 'working_calendar';
    }

    public static function pk()
    {
        return self::$pk;
    }

    public static function getFields()
    {
        return self::$sample;
    }

    /**
     * @return \Atom\Package\Core\Config
     */
    public static function model()
    {
        return parent::model();
    }

    /**
     * 查询
     * @param $data
     * @param array $params
     * @return array
     */
    public function getList(array $params = array())
    {
        $qb = $this->builder();

        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $qb->where($k, $v);
            }
        }

        //TODO 根据查询参数params build sql.
        $ret = $qb->where('status', '=', '1')
            ->hash(self::$pk)
            ->get();
        return $ret;
    }

    /**
     * 查询
     * @param $data
     * @param array $params
     * @return array
     */
    public function getListByDate(array $params = array())
    {
        if (empty($params['start_date']) || empty($params['end_date'])) {
            return false;
        }

        $qb = $this->builder();

        $where = ' date >= :start_date and date <= :end_date ';
        $qb->where($qb->raw($where, array(
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        )));

        //TODO 根据查询参数params build sql.
        $ret = $qb->where('status', '=', '1')
            ->hash(self::$pk)
            ->get();
        return $ret;
    }

    /**
     * 根据Id查询
     * @param int $id
     * @return array
     */
    public function getDataById($id)
    {
        if (is_array($id)) {
            $res = $this->builder()->whereIn(self::$pk, $id)->where('status', '=', '1')->get();
        } else {
            $res = $this->builder()->where(self::$pk, '=', $id)->where('status', '=', '1')->get();
        }

        return $res;
    }


    /**
     * 获取信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getDataList($params = array(), $offset = 0, $limit = 100)
    {

        if (!is_array($params)) {
            return FALSE;
        }
        //查询
        $builder = $this->builder();
        //$builder->select(static::$col);

        // id
        if (isset($params['id'])) {
            if (is_array($params['id'])) {
                $builder->whereIn('id', $params['id']);
            } else {
                $builder->where('id', '=', $params['id']);
            }
        }
// date
        if (isset($params['date']) || isset($params['date_end'])) {
            $builder->where(function ($builder) use ($params) {
                if (isset($params['date']) && isset($params['date_end'])) {
                    $builder->whereBetween('date', $params['date'], $params['date_end']);
                } elseif (isset($params['date_end'])) {
                    $builder->where('date', '<=', 'date_end');
                } elseif (isset($params['date'])) {
                    $builder->where('date', '>=', $params['date']);
                }
            });
        }

// type
        if (isset($params['type'])) {
            if (is_array($params['type'])) {
                $builder->whereIn('type', $params['type']);
            } else {
                $builder->where('type', '=', $params['type']);
            }
        }

// title
        if (!empty($params['title'])) {
            if (is_array($params['title'])) {
                $builder->whereIn('title', $params['title']);
            } else {
                $builder->where('title', '=', $params['title']);
            }
        }

// status
        if (isset($params['status'])) {
            if (is_array($params['status'])) {
                $builder->whereIn('status', $params['status']);
            } else {
                $builder->where('status', '=', $params['status']);
            }
        }

// create_time
        if (!empty($params['create_time'])) {
            if (is_array($params['create_time'])) {
                $builder->whereIn('create_time', $params['create_time']);
            } else {
                $builder->where('create_time', '=', $params['create_time']);
            }
        }

//        $builder->hash(static::pk());
//        $queryObj = $builder->getQuery();
//        return $queryObj->getRawSql();

        $builder->orderBy(static::pk(), 'desc');

        if (isset($params['count']) && $params['count'] == 1) {
            return $builder->count();
        }
        if (isset($params['all']) && $params['all'] == 1) {
            return $builder->hash(static::pk())->get();
        }
        return $builder->hash(static::pk())->offset($offset)->limit($limit)->get();
    }

    //更新数据
    public function updateById($params)
    {
        $pk = static::pk();
        if (isset($params[$pk]) && $params[$pk] > 0) {
            $id = intval($params[$pk]);
            unset($params[$pk]);
            return $this->builder()
                ->where($pk, $id)->update($params);
        }

        return FALSE;
    }

    //添加数据
    public function insert($params)
    {
        if (empty($params)) {
            return FALSE;
        }
        $params = array_intersect_key($params, self::$sample);
        $params = array_merge(self::$sample, $params);
        unset($params[static::pk()]);
        return $this->builder()->insert($params);
    }

    //删除 可扩充
    public function deleteById($id)
    {
        if (isset($id) && $id > 0) {
            return $this->builder()->deleteById($id);
        }

        return FALSE;
    }

}
