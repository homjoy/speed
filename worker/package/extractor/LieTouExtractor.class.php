<?php
namespace Worker\Package\Extractor;

use Worker\Package\Utils\RegexMatcher;
use Worker\Package\Utils\Utils;


/**
 *
 * Class LieTouExtractor
 * @package Worker\Package\Extractor
 */
class LieTouExtractor
{
    static public function resume(&$html){
        $matcher = new RegexMatcher($html);
        $resume = array();

        $resume['info'] = $matcher->one('/resume-info[\s\S.]*?简历编号：(?<resume_id>\d{0,10}?)\s*\|\s*最新登录：(?<last_login>.*?)<\/p>/m');

        $resume['head_img'] = $matcher->one('/section-basic.*?个人信息.*?section-content.*?<img\s*src="(?<head_img>.*?)"\s*class=\"middleFace\"\s*\/>/m');
        $resume['username'] = $matcher->one('/section-basic.*?个人信息.*?section-content.*?姓名：.*?<td\s*data-selector=\"field-name\"><span\sclass=\"init-data\">(?<username>.*?)<\/span><\/td>/m');
        $resume['phone'] = $matcher->one('/section-basic.*?个人信息.*?section-content.*?手机号码：<\/th>.*?<span\sclass=\"init-data\s*\">(?<phone>\d{11}?).*?<\/span>\s*<\/td>/m');
        $resume['email'] = $matcher->one('/section-basic.*?个人信息.*?section-content.*?电子邮件：.*?init-data\">(?<email>.*?)(\s*已验证)?<\/span>\s*<\/td>/m');

        $resume['gender'] = $matcher->one(self::getValueByName('性别'));
        $resume['age'] = $matcher->one(self::getValueByName('年龄'));
        $resume['academic'] = $matcher->one(self::getValueByName('教育程度'));
        $resume['work_year'] = $matcher->one(self::getValueByName('工作年限'));
        $resume['marriage'] = $matcher->one(self::getValueByName('婚姻状况'));
        $resume['work_status'] = $matcher->one(self::getValueByName('职业状态'));
        $resume['location'] = $matcher->one(self::getValueByName('所在地'));
        $resume['country'] = $matcher->one(self::getValueByName('国籍'));
        $resume['census'] = $matcher->one(self::getValueByName('户籍'));


        $resume['work_status'] = array(
            'industry' => $matcher->one(self::getValueByName('所在行业')),
            'company' => $matcher->one(self::getValueByName('公司名称')),
            'job_name' => $matcher->one(self::getValueByName('所任职位')),
            'salary' => $matcher->one(self::getValueByName('目前年薪')),
        );

        $resume['expect'] = array(
            'industry' => $matcher->one(self::getValueByName('期望行业')),
            'job' => $matcher->one(self::getValueByName('期望职位')),
            'work_place' => $matcher->one(self::getValueByName('期望地点')),
            'salary' => $matcher->one(self::getValueByName('期望年薪')),
        );


        $workExp = $matcher->all('/总体信息[\s\S.]*?<.*?timestamp.*?>(?<start_time>.*?)-(?<end_time>.*?)<\/span>[\s\S.]*?filter-zone\">(?<company>.*?)<\/span>[\s\S.]*?time-total\">(?<total_time>.*?)<\/span>[\s\S.]*?<!--\s*公司性质\s*\|\s*人数\s*\|\s*行业\s*-->\s*<td>\s*(&nbsp;&nbsp;&nbsp;)?([\s\S.]*?\|?公司性质：(?<property>.*?))?(\|?\s*公司规模：(?<scale>.*?))?(\|?\s*公司行业：(?<industry>.*?))?<\/td>([\s\S.]*?公司描述：<span.*?>(?<com_desp>.*?)<\/span><\/td>)?[\s\S.]*?<!--\s*历任职位\s*-->[\s\S.]*?list-title\s*">(?<jobs>[\s\S.]*?)section-content/m');
        if(!empty($workExp)){
            foreach($workExp as $i=>$exp){
                $workExp[$i]['jobs'] = Utils::matchAll($exp['jobs'],'/timestamp.*?>(?<start_time>.*?)-(?<end_time>.*?)<\/span>.*?filter-zone\"\s*>(?<position>.*?)<\/span>(.*?所在地区：<\/th>\s*<td.*?>(?<location>.*?)<\/td>)?(.*?所在部门：<\/th>\s*<td.*?>(?<department>.*?)<\/td>)?(.*?汇报对象：<\/th>\s*<td.*?>(?<leader>.*?)<\/td>)?(.*?下属人数：<\/th>\s*<td.*?>(?<subordinate>.*?)<\/td>)?(.*?薪酬状况：<\/th>\s*<td.*?>(?<salary>.*?)<\/td>)?(.*?工作职责：<\/th>\s*<td.*?>(?<resp>.*?)<\/td>)?(.*?工作业绩：<\/th>\s*<td.*?>(?<performance>.*?)<\/td>)?/m');
            }
            $resume['work_exp'] = $workExp;
        }else{
            $resume['work_exp'] = array();
        }

        $resume['project_exp'] = $matcher->multi(array(
            array('one',self::getBlockBetween('项目经历','教育经历')),
            array('all','/<.*?timestamp.*?>(?<start_time>.*?)–(?<end_time>.*?)<\/span>\s*<span.*?>(?<project>.*?)<\/span>[\s\S.]*?([\s\S.]*?项目职务：<\/th>\s*<td.*?>(?<position>.*?)<\/td>)?([\s\S.]*?所在公司：<\/th>\s*<td.*?>(?<company>.*?)<\/td>)?([\s\S.]*?项目简介：<\/th>\s*<td.*?>(?<description>.*?)<\/td>)?([\s\S.]*?项目业绩：<\/th>\s*<td.*?>(?<performance>.*?)<\/td>)?/m')
        ));


        $resume['project_exp'] = $matcher->multi(array(
            array('one','/section-education.*?教育经历.*?section-content.*?tbody>(?<edu_exp>.*?)<\/tbody>/m'),
            array('all','/学校\s*-->\s*<td.*?><strong>(?<school>.*?)<\/strong>(?<start_time>.*?)–(?<end_time>.*?)<\/td>.*?专业：<span.*?>(?<major>.*?)<\/span><\/td>.*?学历：<span.*?>(?<academic>.*?)<\/span><\/td>\s*<\/tr>/m')
        ));

        $resume['self_assessment'] = $matcher->one(self::getBlockByName('自我评价'));
        $resume['language_ability'] = $matcher->one(self::getBlockByName('语言能力'));
        $resume['other_info'] = $matcher->one(self::getBlockByName('附加信息'));

        return $resume;
    }

    static protected function getValueByName($name)
    {
        return "/".$name.".*?<\/th>\s*<td.*?>(?<value>.*?)<\/td>/m";
    }

    static protected function getBlockByName($name)
    {
        return '/'.$name.'.*?<tbody>\s*<tr>\s*<td.*?>(?<data>.*?)<\/td>/m';
    }

    static protected function getBlockBetween($start,$end)
    {
        return '/<!-- '.$start.' -->(?<data>[\s\S.]*?)<!-- '.$end.' -->/m';
    }
}