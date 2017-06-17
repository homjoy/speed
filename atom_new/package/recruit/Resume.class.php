<?php
namespace Atom\Package\Recruit;

use Atom\Package\Common\BaseQuery;

/**
 * Class Resume
 * @package Atom\Package\Recruit
 */
class Resume extends BaseQuery
{
    const SRC_MLS_WEB = 'mls_web'; //主站
    const SRC_MLS_APP = 'mls_app'; //美丽说APP
    const SRC_MLS_WECHAT = 'mls_wechat'; //美丽说微信
    const SRC_MLS_CAMPUS = 'mls_campus'; //美丽说校招
    const SRC_51JOB = '51job';  //51JOB
    const SRC_ZHAOPIN = 'zhaopin';  //智联招聘
    const SRC_LIEPIN = 'liepin';    //猎聘网
    const SRC_LAGOU = 'lagou';  //拉勾网
    const SRC_58TC = '58tc';  //58同城
    const SRC_GANJI = 'ganji';  //赶集网
    const SRC_OTHER = 'other';  //其他招聘渠道

    /**
     * 数据库表名
     * @return string
     */
    public static function tableName()
    {
        return 'resume';
    }

    /**
     * @return \Atom\Package\Recruit\Resume
     */
    public static function model()
    {
        return parent::model();
    }


    /**
     * 简历搜索
     * @param array $params
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function searchResumeList(array $params = array(), $page = 1, $pageSize = 20)
    {
        $page = $page < 1 ? 1 : $page;
        $qb = $this->builder();

        //TODO 完善查询参数
        //简历来源
        if (!empty($params['source'])) {
            $qb = $qb->where('source', $params['source']);
        }
        //工作年限
        if (is_int($params['workYear'])) {
            $qb = $qb->where('work_year', '>=', $params['workYear']);
        }elseif(is_array($params['workYear'])){
            $qb = $qb->whereBetween('work_year',$params['workYear'][0], $params['workYear'][1]);
        }

        //学历
        if (isset($params['academic'])) {
            $qb = $qb->where('academic', $params['academic']);
        }

        $ret = $qb->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();
        return array($qb->count(),$ret);
    }


    /**
     * 获取简历信息
     * @param $id
     * @param array $fields
     * @return array
     */
    public function getResumeById($id, $full = false, $fields = array())
    {
        if (empty($fields)) {
            $fields = '*';
        }

        $resume = $this->builder()->select($fields)->find($id);

        //获取完整的简历信息
        if (!empty($resume)) {
            $resume['other_info'] = json_decode($resume['other_info'],true);
            $resume['extend'] = !$full ? array() :
                $this->builder(ResumeExtend::tableName())->findAll('resume_id', $id);
            foreach($resume['extend'] as &$ext)
            {
                $ext['extend_info'] = json_decode($ext['extend_info'],true);
            }
        }


        return $resume;
    }

    /**
     * 根据手机号或者邮箱获取简历
     * @param string $phone
     * @param string $email
     * @return null|array
     */
    public function getResumeByPhoneOrEmail($phone = '',$email = '',$full=false)
    {
        if(empty($phone) && empty($email)){
            return null;
        }

        $qb = $this->builder();

        if($phone !== ''){
            $qb->orWhere('phone',$phone);
        }
        if($email !== ''){
            $qb->orWhere('email',$email);
        }
        $resume = $qb->first();
        if (!empty($resume)) {
            $resume['other_info'] = json_decode($resume['other_info'],true);
            $resume['extend'] = !$full ? array() :
                $this->builder(ResumeExtend::tableName())->findAll('resume_id', $resume[static::pk()]);
            foreach($resume['extend'] as &$ext)
            {
                $ext['extend_info'] = json_decode($ext['extend_info'],true);
            }
        }

        return $resume;
    }

    /**
     * 增加简历信息
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function insert($data)
    {
        $extend = isset($data['extend']) ? $data['extend'] : array();
        unset($data['extend']);
        try {
            $data['other_info'] = json_encode($data['other_info']);
            $resumeId = $this->builder()->insert($data);

            foreach ($extend as $index => $ext) {
                $extend[$index]['resume_id'] = $resumeId;
                $extend[$index]['extend_info'] = json_encode($ext['extend_info']);
            }

            $extIds = !empty($extend) ?
                $this->builder(ResumeExtend::tableName())->insert($extend) : array();

            return compact('resumeId', 'extIds');
        } catch (\Exception $e) {
            //删除脏数据
            if (!empty($resumeId)) {
                $this->builder()->where('id', $resumeId)->delete();
                $this->builder(ResumeExtend::tableName())->where('resume_id', $resumeId)->delete();
            }
            throw $e;
        }
    }

    /**
     * 保存简历 存在则更新，不存在则插入
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function saveOrUpdate($data)
    {
        $resume = $this->builder()
            ->where('phone', $data['phone'])
            ->orWhere('email', $data['email'])
            ->first();
        $data['other_info'] = json_encode($data['other_info']);

        if (empty($resume)) {
            return $this->insert($data);
        }

        $extend = isset($data['extend']) ? $data['extend'] : array();
        unset($data['extend']);

        $row = $this->builder()->where('id', $resume['id'])
            ->update($data);

        /*
         * TODO 目前的处理策略是根据是否有主键来区别的，相同信息需要在抓取提取的时候进行检测
         */
        $extIds = array();
        foreach ($extend as $ext) {
            $id = isset($ext['id']) ? intval($ext['id']) : 0;
            unset($ext['id']);
            $builder = $this->builder(ResumeExtend::tableName());
            if (!empty($id)) {
                $builder->where('id',$id)->update($ext);
            } else {
                $ext['resume_id'] = $resume['id'];
                $ext['extend_info'] = json_encode($ext['extend_info']);
                $id = (int)$builder->insert($ext);
            }
            $extIds[] = $id;
        }

        return array('resumeId'=>(int)$resume['id'],'extIds'=>$extIds);
    }
}