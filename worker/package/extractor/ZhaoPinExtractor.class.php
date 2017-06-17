<?php
namespace Worker\Package\Extractor;

use Worker\Package\Common\Constant;
use Worker\Package\Utils\RegexMatcher;


/**
 * 智联招聘简历信息提取
 * Class ZhaoPinExtractor
 * @package Worker\Package\Extractor
 */
class ZhaoPinExtractor
{
    /**
     * 检查是否是登陆状态，登陆成功之后都会进入选择登陆机构页面
     * @param $html
     * @return bool
     */
    static public function checkLogin($html)
    {
        $regex = '/<title>我的网聘-选择登陆机构<\/title>/m';
        if( preg_match($regex,$html,$matches) > 0 ){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 获取职位下的简历列表
     * @param $html
     * @return array
     */
    static public function positionResumeList(&$html)
    {
        $matcher = new RegexMatcher($html);
        return $matcher->all('/<tr\sclass=\"list\d\"[\s\S]*?>[\s\S]*?data-resumebh=\"(?<code>.*?)\".*?data-resguid=\"(?<resume_id>.*?)\".*?userinfo_forsend=\"(?<userinfo>.*?)\"[\s\S]*?<a.*?href=\"(?<link>.*?)\".*?class=\"link[\s\S]*?<td\s*?title=\"(?<post_time>.*?)\">[\s\S]*?<\/td>[\s\S]*?<\/tr>/m');
    }

    /**
     * 获取职位列表
     * @param $html
     * @return array
     */
    static public function positionList(&$html)
    {
        $matcher = new RegexMatcher($html);
        $jobList = $matcher->all('/<tr\sclass=\"list\d\"[\s\S]*?>[\s\S]*?name=\"jobtitle_\d+".*?value="(?<title>.*?)"[\s\S]*?id="resumecount_(?<position_id>\d+).*?job_status="(?<status>.*?)"\s*?is_feedback="(?<is_feedback>.*?)\"[\s\S]*?<\/tr>/m');
        foreach($jobList as &$job){
            $job['title'] = html_entity_decode($job['title']);
        }
        return $jobList;
    }

    /**
     * 提取简历~
     * @param $html
     * @return array
     * @throws \Exception
     */
    static public function resume(&$html){
        $matcher = new RegexMatcher($html);
        $resume = array();

        $resume['outer_id'] = (string)$matcher->one('/<input.*?id=\"guid\".*?value=\"(?<guid>\d+)\".*?>/m');
        //$resume['resume_id'] = $matcher->one('/<span\sclass=\"resume-left-tips-id\">ID:(?<resume_id>.*?)<\/span>/m');
        $resume['outer_uid'] = $matcher->one('/<input\s*?type="hidden"\s*?name="resumeUserId"\s*?id="resumeUserId"\s*?value="(?<user_id>\d+)"\s*?\/>/m');
        $resume['source'] = 'zhaopin';
        $resume['name_cn'] =  (string)$matcher->one('/<div\s*?id=\"userName\".*?>(?<username>.*?)<\/div>/m');
        $resume['name_en'] =  '';
        $resume['phone'] =  (string)$matcher->one('/手机：(?<phone>\d{11})/m');
        $resume['email'] =  (string)$matcher->one('/E-mail：<a\s?href=\"mailto:.*?\">(?<email>.*?)<\/a>/m');
        $resume['photo'] =  (string)$matcher->one('/class=\"summary\">[\s\S]*?<img.*?src=\"(?<heah_img>.*?)\".*?class=\"headerImg\"/m');

        //简历总结信息
        $summary = $matcher->one('/<div\s*?class=\"summary-top\">[\s\S]*?<span>(?<gender>.*?)(?:&nbsp;){4}(?<age>.*?)\((?<birthday>.*?)\)((?:&nbsp;){4}(?<experience>.*?))?((?:&nbsp;){4}(?<education>.*?))?((?:&nbsp;){4}(?<marriage>.*?)?)?<\/span>/m');
        $resume['gender'] = $summary['gender'] == '女' ? 0 : 1;
        $resume['age'] = intval($summary['age']);
        $resume['work_year'] = intval($summary['experience']);
        $resume['academic'] = (string)$summary['education'];
        $resume['marriage'] = $summary['marriage'] == '未婚' ? 0 : 1;

        $locations = $matcher->one('/现居住地：(?<location>[\s\S]*?)(\|\s*?户口：(?<census>[\s\S]*?))?<\/div>/m');
        $resume['city'] = isset($locations['location'])? (string)$locations['location'] : '';

//        $resume['census'] = isset($locations['census'])? $locations['census'] : '';
//        $resume['identify_num'] =  $matcher->one('/身份证：(?<identify>[\da-zA-z]{18})/m');

       /* $postJob = $matcher->all("/<strong\\s*?.*?>(.*?)<\\/strong>/m");
        $resume['post_job'] = array(
            'job_name' => isset($postJob[0]) ? $postJob[0] : '',
            'company' => isset($postJob[1]) ? $postJob[1] : '',
            'work_place' => isset($postJob[2]) ? $postJob[2] : '',
        );*/

        $expectInfo = $matcher->one('/class=\"resume-preview-top\">[\s\S]*?'
            .'期望工作地区：<\/td>\s*?<td>(?<work_place>.*?)<\/td>[\s\S]*?'
            .'期望月薪：<\/td>\s*?<td>(?<salary>.*?)<\/td>[\s\S]*?'
            .'目前状况：<\/td>\s*?<td>(?<current_status>.*?)<\/td>[\s\S]*?'
            .'期望工作性质：<\/td>\s*?<td>(?<job_property>.*?)<\/td>[\s\S]*?'
            .'期望从事职业：<\/td>\s*?<td>(?<job>.*?)<\/td>[\s\S]*?'
            .'期望从事行业：<\/td>\s*?<td>(?<industry>.*?)'
            .'<\/td>[\s\S]*?<\/tr>[\s\S]*?<\/table>[\s\S]*?<\/div>[\s\S]*?<\/div>/m');
        //求职意向
        $resume['expect_industry'] = (string)$expectInfo['industry'];
        $resume['expect_job'] = (string)$expectInfo['job'];
        $resume['expect_property'] = (string)$expectInfo['job_property'];
        $resume['expect_city'] = (string)$expectInfo['work_place'];
        $resume['expect_salary'] = (string)$expectInfo['salary'];
        $resume['expect_work_time'] = '';

        //$resume['current_status'] = 1;//'我目前处于离职状态，可立即上岗';

        //自我评价
        $resume['self_assessment'] = (string)$matcher->one('/resume-preview-dl\srd-break\">(?<assessments>.*?)<\/div>/m');

        //简历内容
        $resume['other_info'] = (string)$matcher->one('/简历内容<\/h3>\s*<div class="resume-preview-dl">(?<resume_detail>.*?)<\/div>/m');



        $resume['extend'] = array();
        $workExp = $matcher->multi(array(
            array('one','/workExperience\">[\s\S]*?工作经历<\/h3>\s*?(?<work_exp>[\s\S]*?)<\/div>\s*<\/div>\s*<div\sclass=\"resume-preview-all\">/m'),
            array('all','/<h2>(?<start_time>.*?)\s-\s(?<end_time>.*?)&nbsp;&nbsp;(?<compay>.*?)(&nbsp;&nbsp;\s*?（(?<total_time>.*?)）)?<\/h2>\s*?<h5>(?<position>.*?)(\|(?<salary>.*?))?<\/h5>[\s\S]*?<div\sclass=\"resume-preview-dl\">\s*?(?<industry>[^\s].*?)(\|\s*?企业性质：(?<property>.*?))?(\|\s*?规模：(?<company_scale>[^\s].*?))?\s*?<\/div>[\s\S]*?<td.*?>工作描述：<\/td>\s*?<td>(?<description>[\s\S]*?)<\/td>\s*?<\/tr>\s*?<\/table>/m')
        ));
        //工作经验
        foreach($workExp as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_WORK,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => (string)$ext['total_time'],
                'title' => (string)$ext['company'],
                'value1' => (string)$ext['position'],
                'value2' => (string)$ext['salary'],
                'value3' => (string)$ext['industry'],
                'value4' => (string)$ext['description'],
                'extend_info' => array(
                    'property' => (string)$ext['property'],
                    'company_scale' => (string)$ext['company_scale'],
                ),
            );
        }

        //教育经历
        //$educations = $matcher->all('/educationContent\">(?<start_time>[\s\S]*?)-(?<end_time>\s*?\d+?\.\d+)(&nbsp;&nbsp;|\s*?)(?<school>[^\s.]*?)(&nbsp;&nbsp;|\s*?)(?<major>[^\s.]*?)(&nbsp;&nbsp;|\s*?)(?<academic>[^\s.]*?)<br[\s\S]*?\s<\/div>/m');
        $educations = $matcher->all('/educationContent\">(?<start_time>[\s\S]*?)-(?<end_time>\s*?\d+?\.\d+)&nbsp;&nbsp;(?<school>[^\s.]*?)&nbsp;&nbsp;(?<major>[^\s.]*?)&nbsp;&nbsp;(?<academic>[^\s.]*?)<br[\s\S]*?\s<\/div>/m');
        foreach($educations as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_EDUCATION,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => '',
                'title' => (string)$ext['school'],
                'value1' => (string)$ext['major'],
                'value2' => (string)$ext['academic'],
                'value3' => '',
                'value4' => '',
                'extend_info' => array(),
            );
        }


        //专业技能
        $skills = $matcher->all('/专业技能<\/h3>\s*<div\sclass=\"resume-preview-dl\">(?<name>.*?)：(?<level>.*?)\|(?<time>.*?)<br.*?<\/div>\s*<\/div>/m');
        foreach($skills as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_SKILL,
                'start_time' => '',
                'end_time' => '',
                'total_time' => (string)$ext['time'],
                'title' => (string)$ext['name'],
                'value1' => (string)$ext['level'],
                'value2' => '',
                'value3' => '',
                'value4' => '',
                'extend_info' => array(),
            );
        }

        //实践经验
        $practices = $matcher->all('/在校实践经验<\/h3>.*?<h2>(?<start_time>.*?)\s-\s(?<end_time>.*?)&nbsp;&nbsp;(?<place>.*?)<\/h2>(.*?实践描述：<\/td>\s*<td>(?<detail>.*?)<\/td>\s*<\/tr>)?/m');
        foreach($practices as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_PRACTICE,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['start_time'],
                'total_time' => '',
                'title' => (string)$ext['place'],
                'value1' => '',
                'value2' => '',
                'value3' => '',
                'value4' => (string)$ext['detail'],
                'extend_info' => array(),
            );
        }

        return $resume;
    }
}