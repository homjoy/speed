<?php
namespace Worker\Package\Extractor;

use Worker\Package\Common\Constant;
use Worker\Package\Utils\RegexMatcher;

/**
 * Class Job51Extractor
 * @package Worker\Package\Extractor
 */
class Job51Extractor
{

    /**
     * 提取Inbox/InboxViewEngine页面的简历列表
     * @param $html
     * @return array
     */
    static public function inboxView(&$html)
    {
        $regex = '/id=\"trBaseInfo_(?<index>\d+)\"[\s\S.]*?<input[\s\S.]*?value=[\"\'](?<resume_id>\d+)[\"\']\s*value1=[\"\'](?<user_id>\d+)[\"\']\s*value2=[\"\'](?<job_id>\d+)[\"\']\s*value3=[\"\'](?<known_id>\d+)[\"\'][\s\S.]*?inbox_td6\".*?>(?<match_percent>\d+)\%<\/td>[\s\S.]*?inbox_td4".*?>(?<flow>.*?)<\/td>[\s\S.]*?inbox_td11\">[\s\S.]*?href=[\"\']\/Candidate\/ResumeViewFolder\.aspx[\s\S.]*?>(?<name>.*?)<\/a>[\s\S.]*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>(?<sex>.*?)<\/td>[\s\S]*?<td.*?>(?<age>.*?)<\/td>[\s\S]*?<td.*?>(?<academic>.*?)<\/td>[\s\S]*?<td.*?>(?<work_year>.*?)<\/td>[\s\S]*?<td.*?>(?<job>.*?)<\/td>[\s\S]*?<td.*?>(?<post_date>.*?)<\/td>[\s\S.]*?<\/tr>/m';
        $matcher = new RegexMatcher($html);
        return $matcher->all($regex);
    }

    /**
     * 登陆检测
     * @param $html
     * @return array|bool
     */
    static public function checkLogin($html)
    {
        $html=preg_replace("/[\t\n\r]+/","",$html);
        $homePage = '/id=\"Navigate_HrUName\">.*?id=\"lblWelecome\">您好！.*?id=\"lblOnlineUserTip\".*?贵公司目前用户数：/m';
        $conflictRegex = '/对不起，您已通过其他浏览器登录到本系统；或者是您上次登录后没有正常退出！请您点击/m';

        if( preg_match($homePage,$html,$matches) > 0 ){
            return true;
        }else{
            //如果被其他浏览器登陆了.
            if(preg_match($conflictRegex,$html,$matches) > 0 ){
                $matcher = new RegexMatcher($html);
                return (array)$matcher->one('/<tr.*?>\s*?<td.*?>(?<username>.*?)<\/td><td.*?>(?<phone>.*?)<\/td><td.*?>(?<login_time>.*?)<\/td><td.*?>(?<login_ip>.*?)<\/td><td.*?><a\s*?href="javascript:.*?">强制下线<\/a><\/td>\s*?<\/tr>/m');
            }else{
                return false;
            }
        }
    }

    /**
     * 提取Candidate/ResumeViewFolder页面的简历信息
     * @param $html
     * @return array
     */
    static public function resume(&$html)
    {
        $resume = array();
        $matcher = new RegexMatcher($html,true);


        $resume['post_job'] = array(
            'title' => $matcher->one('/应聘职位：<span.*?>(?<job_name>.*?)<\/span>/m'),
            'company' => $matcher->one('/应聘公司：<span.*?>(?<company>.*?)<\/span>/m'),
            'post_time' => $matcher->one('/投递时间：<span.*?>(?<post_time>.*?)<\/span>/m'),
            'update_time' => $matcher->one('/更新时间：<span.*?>(?<update_time>.*?)<\/span>/m'),
        );

        $resume['outer_id'] = (string)$matcher->one('/<input\s*?type="hidden"\s*?name="hidSeqID"\s*?id="hidSeqID"\s*?value="(?<seq_id>\d+)"\s*?\/>/m');
        $resume['source'] = '51job';
        $resume['outer_uid'] = (string)$matcher->one('/<input\s*?type="hidden"\s*?name="hidUserID"\s*?id="hidUserID"\s*?value="(?<user_id>\d+)"\s*?\/>/m');

        $photo = (string)$matcher->one('/<\/b><\/span><\/td><td[\s\S.]*?valign=\"middle\">[\s\S.]*?<img.*?src=\"(?<head_img>[^\s].*?)\"\swidth=\"90\"\sheight=\"110\">[\s\S]*?<\/td>/m');
        $resume['photo']  = str_replace('../', 'http://eWorker.51job.com/', $photo);
        $resume['name_cn'] = $matcher->one('/<span\sstyle=\"font-size:25px;\"><b>(?<username>.*?)<\/b><\/span>/m');
        $resume['name_en'] = '';
        $resume['phone'] = $matcher->one('/<td.*?>电　话：<\/td>\s*<td.*?>((?<phone>\d{11})（手机）)?.*?<\/td>/');
        $resume['email'] = $matcher->one('/<td.*?>E-mail：<\/td>\s*<td.*?><a.*?href=".*?">(?<email>.*?)<\/a><\/td>/m');

        $userInfo = $matcher->one('/<span\sclass=\"blue\"><b>(?<exp_year>.*?)\s*\|\s*&nbsp;(?<gender>.*?)\s*\|\s*&nbsp;(?<age>.*?)（(?<birthday>.*?)）(\s*\|\s*&nbsp;(?<marriage>.*?))?(\s*\|\s*&nbsp;(?<user_height>.*?))?(\s*\|\s*&nbsp;(?<political>.*?))?<\/b><\/span>/m');
        $resume['work_year'] = (int)$userInfo['exp_year'];
        $resume['gender'] = $userInfo['gender'] == '男' ? 1 : 0;
        $resume['age'] = (int)$userInfo['age'];
        $resume['marriage'] = (string)$userInfo['marriage'];
        $resume['birthday'] = (string)$userInfo['birthday'];

        $resume['other_info'] = array();
        //身高
        $resume['other_info']['user_height'] = (string)$userInfo['user_height'];
        //政治面貌
        $resume['other_info']['political'] = (string)$userInfo['political'];
        $resume['city'] = $matcher->one('/<td.*?>居住地：<\/td>\s*<td.*?>(?<location>.*?)<\/td>/m');
        $resume['academic'] = $matcher->one('/<td.*?>学　历：<\/td>\s*<td.*?>(?<education>.*?)<\/td>/m');


        //自我评价
        $resume['self_assessment'] = $matcher->one('/<td.*?class=\"text_left\"><span\sclass=\"text\">(?<assessment>.*?)<\/span><\/td>/m');

        $resume['current_salary_year'] = (string)$matcher->one('/目前年薪：\s*<\/td><td.*?>(?<value>[\s\S.]*?)<\/td>/m');
        $resume['salary_base'] = (string)$matcher->one('/基本工资：\s*<\/td><td.*?>(?<value>[\s\S.]*?)<\/td>/m');
        $resume['bonus'] = (string)$matcher->one('/年度奖金\/佣金：\s*<\/td><td.*?>(?<value>[\s\S.]*?)<\/td>/m');
        $resume['subsidy'] = (string)$matcher->one('/补贴\/津贴：\s*<\/td><td.*?>(?<value>[\s\S.]*?)<\/td>/m');


        $resume['other_info']['census'] = $matcher->one('/<td.*?>户　口：<\/td>\s*<td.*?>(?<census>.*?)<\/td>/m');
        $resume['other_info']['address'] = $matcher->one('/<td.*?> 地　址： <\/td>\s*<td.*?>(?<address>.*?)<\/td>/m');
        $resume['other_info']['major'] = $matcher->one('/<td.*?>专　业：<\/td>\s*<td.*?>(?<major>.*?)<\/td>/m');
        $resume['other_info']['school'] = $matcher->one('/<td.*?>学　校：<\/td>\s*<td>(?<school>.*?)<\/td>/m');

/*
        $jobName = $matcher->one('/<td.*?>职　位：<\/td>\s*<td.*?>(?<job_name>.*?)<\/td>/m')
            || $matcher->one('/<td.*?>职　能：<\/td>\s*<td.*?>(?<job_name>.*?)<\/td>/m');
        $resume['last_work'] = array(
            'work_time' => $matcher->one('/<b>最近工作<\/b><\/span><span.*?><b>\s*\[(?<work_time>.*?)\s*]\s*<\/b><\/span><\/td>/m'),
            'company' => $matcher->one('/<td.*?>公　司：<\/td>\s*<td.*?>(?<company>.*?)<\/td>/m'),
            'industry' => $matcher->one('/<td.*?>行　业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td>/m'),
            'job' => $jobName,
        );
*/

        $resume['expect_industry'] = $matcher->one('/希望行业：\s*<span.*?>(?<industry>.*?)<\/span><\/td>/m');
        $resume['expect_job'] = $matcher->one('/目标职能：\s*<span.*?>(?<job_name>.*?)<\/span><\/td>/m');
        $resume['expect_property'] = $matcher->one('/工作性质：\s*<span.*?>(?<property>.*?)<\/span><\/td>/m');
        $resume['expect_city'] = $matcher->one('/目标地点：\s*<span.*?>(?<work_place>.*?)<\/span><\/td>/m');
        $resume['expect_salary'] = $matcher->one('/期望月薪：\s*<span.*?>(?<salary>.*?)<\/span><\/td>/m');
        $resume['status'] = $matcher->one('/求职状态：\s*<span.*?>(?<status>.*?)<\/span><\/td>/m');
        $resume['other_info']['come_time'] = $matcher->one('/到岗时间：\s*<span.*?>(?<come_time>.*?)<\/span><\/td>/m');


        $keyValueRegex = '/<tr.*?>\s*<td.*?text_left".*?>(?<name>[\s\S.]*?)<\/td>\s*<td.*?>(?<value>[\s\S.]*?)<\/td>\s*<\/tr>/m';

        $workExp = $matcher->multi(array(
            array('one',self::getBlockRegex('工作经验')),
            /**
             *
             * <tr>\s*<td.*?class=\"text_left\">(?<start_time>[\s\d]+\/\d+?)--(?<end_time>[\s\S]*?)：(?<company>.*?)(\((?<company_scale>.*?)\))??<img[\s\S]*?<span.*?><b>\s*?\[(?<total_time>.*?)\]\s*?<\/b><\/span><\/td><\/tr>(<tr><td.*?>所属行业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td><\/tr>)??<tr><td.*?><b>(?<department>.*?)<\/b><\/td>\s*?<td\sclass=\"text\"><b>(?<position>.*?)<\/b><\/td><\/tr><tr><td.*?>(?<detail>(?!</td>).*?)<\/td><\/tr>(<tr>.*?汇报对象：<\/td><td.*?>(?<leader>.*?)<\/td><\/tr>)??(<tr><td.*?>下属人数：<\/td><td.*?>(?<underling>\d+)<\/td><\/tr>)??(<tr><td.*?>工作业绩：<\/td><td.*?>(?<performance>.*?)<\/td><\/tr><tr><.*?><hr)??
             * array('all','/<tr>\s*<td.*?class=\"text_left\">(?<start_time>[\s\d]+\/\d+?)--(?<end_time>[\s\S.]*?)：(?<company>.*?)(\((?<company_scale>.*?)\))?<img[\s\S.]*?<span.*?><b>\s*\[(?<total_time>.*?)\]\s*<\/b><\/span><\/td>[\s\S.]*?(<td.*?>所属行业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td>)?[\s\S.]*?<td.*?><b>(?<department>.*?)<\/b><\/td>\s*<td\sclass=\"text\"><b>(?<position>.*?)<\/b><\/td>[\s\S.]*?<td.*?>(?<detail>.*?)<\/td>\s*<\/tr>/m'),
             * array('all','/<tr>\s*<td.*?class=\"text_left\">(?<start_time>[\s\d]+\/\d+?)--(?<end_time>[\s\S]*?)：(?<company>.*?)(\((?<company_scale>.*?)\))??<img[\s\S]*?<span.*?><b>\s*?\[(?<total_time>.*?)\]\s*?<\/b><\/span><\/td><\/tr>(<tr><td.*?>所属行业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td><\/tr>)?<tr><td.*?><b>(?<department>.*?)<\/b><\/td>\s*<td\sclass=\"text\"><b>(?<position>.*?)<\/b><\/td><\/tr><tr><td.*?>(?<detail>(?!</td>).*?)<\/td><\/tr>(<tr>.*?汇报对象：<\/td><td.*?>(?<leader>.*?)<\/td><\/tr>)??(<tr><td.*?>下属人数：<\/td><td.*?>(?<underling>\d+)<\/td><\/tr>)??(<tr><td.*?>工作业绩：<\/td><td.*?>(?<performance>.*?)<\/td><\/tr>)??/m'),
             * array('all','/<tr>\s*<td.*?class=\"text_left\">(?<start_time>[\s\d]+\/\d+?)--(?<end_time>[\s\S]*?)：(?<company>.*?)(\((?<company_scale>.*?)\))??<img[\s\S]*?<span.*?><b>\s*?\[(?<total_time>.*?)\]\s*?<\/b><\/span><\/td><\/tr>(<tr><td.*?>所属行业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td><\/tr>)??<tr><td.*?><b>(?<department>.*?)<\/b><\/td>\s*?<td\sclass=\"text\"><b>(?<position>.*?)<\/b><\/td><\/tr><tr><td.*?>(?<detail>(?!</td>).*?)<\/td><\/tr>/m'),
             */
            array('all','/<tr>\s*<td.*?class=\"text_left\">(?<start_time>[\s\d]+\/\d+?)--(?<end_time>[\s\S.]*?)：(?<company>.*?)(\((?<company_scale>.*?)\))?<img[\s\S.]*?<span.*?><b>\s*\[(?<total_time>.*?)\]\s*<\/b><\/span><\/td>[\s\S.]*?(<td.*?>所属行业：<\/td>\s*<td.*?>(?<industry>.*?)<\/td>)?[\s\S.]*?<td.*?><b>(?<department>.*?)<\/b><\/td>\s*<td\sclass=\"text\"><b>(?<position>.*?)<\/b><\/td>[\s\S.]*?<td.*?>(?<detail>.*?)<\/td>\s*<\/tr>/m'),
        ));
        //工作经验
        foreach($workExp as $ext){
            $resume[' '][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_WORK,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => (string)$ext['total_time'],
                'title' => (string)$ext['company'],
                'value1' => (string)$ext['position'],
                'value2' => (string)$ext['industry'],
                'value3' => (string)$ext['department'],
                'value4' => (string)$ext['detail'],
                'extend_info' => array(
                    'company_scale' => (string)$ext['company_scale'],
                    'leader' => (string)$ext['leader'],
                    'underling' => (string)$ext['underling'],
                    'performance' => (string)$ext['performance'],
                ),
            );
        }

        $projectExp = $matcher->multi(array(
            array('one',self::getBlockRegex('项目经验')),
            /**
             * array('all','/<tr>\s*<td.*?class=\"text_left\">(?<start_time>.*?)--(?<end_time>.*?)：(?<project>.*?)<\/td>[\s\S.]*?<td.*?>软件环境：<\/td>\s*<td.*?>(?<env_software>.*?)<\/td>[\s\S.]*?硬件环境：<\/td>\s*<td.*?>(?<env_hardware>.*?)<\/td>[\s\S.]*?开发工具：<\/td>\s*<td.*?>(?<dev_tool>.*?)<\/td>[\s\S.]*?项目描述：<\/td>\s*<td.*?>(?<project_desp>.*?)<\/td>[\s\S.]*?责任描述：<\/td>\s*<td.*?>(?<resp_detail>.*?)<\/td>\s*<\/tr>/m'),
             */
            array('all','/<tr>\s*?<td.*?>(?<start_time>[\s\d]+\/\d+?)--(?<end_time>.*?)?：(?<project>.*?)<\/td><\/tr>(<tr><td.*?>\s*?软件环境：<\/td>\s*<td.*?>(?<env_software>.*?)<\/td><\/tr>)??(<tr><td.*?>\s*?硬件环境：<\/td><td.*?>(?<env_hardware>.*?)<\/td><\/tr>)??(<tr><td.*?>\s*?开发工具：<\/td><td.*?>(?<dev_tool>.*?)<\/td><\/tr>)??<tr><td.*?>项目描述：<\/td>\s*<td.*?>(?<project_desp>.*?)<\/td><\/tr>.*?责任描述：<\/td>\s*<td.*?>(?<resp_detail>.*?)<\/td>\s*<\/tr>/m'),
        ));
        //项目经验
        foreach($projectExp as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_PROJECT,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => (string)$ext['total_time'],
                'title' => (string)$ext['project'],
                'value1' => (string)$ext['env_software'],
                'value2' => (string)$ext['env_hardware'],
                'value3' => (string)$ext['dev_tool'],
                'value4' => (string)$ext['project_desp'],
                'extend_info' => array(
                    'detail' => (string)$ext['resp_detail'],
                    'department' => '',
                    'property' => '',
                    'company_scale' => (string)$ext['company_scale'],
                ),
            );
        }


        $skills = $matcher->multi(array(
            array('one',self::getBlockRegex('IT 技能')),
            array('all','/<tr>\s*<td.*?>(?<name>.*?)<\/td>\s*<td.*?>(?<level>.*?)<\/td>\s*<td.*?>(?<used_time>.*?)<\/td>\s*<\/tr>/m'),
        ));
        //技能
        foreach($skills as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_SKILL,
                'start_time' => '',
                'end_time' => '',
                'total_time' => (string)$ext['used_time'],
                'title' => (string)$ext['name'],
                'value1' => '',
                'value2' => '',
                'value3' => '',
                'value4' => (string)$ext['level'],
                'extend_info' => array()
            );
        }


        $certs = $matcher->multi(array(
            array('one',self::getBlockRegex('证<img.*?\/?>书\s*')),
            array('all','/<tr>\s*<td.*?text_left\"\s*>(?<cert_time>[\s\S.]*?)<\/td>\s*<td.*?>(?<name>[\s\S.]*?)<\/td>\s*<td.*?>(?<ext>.*?)<\/td>\s*<\/tr>/m'),
        ));
        //证书
        foreach($certs as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_CREDENTIAL,
                'start_time' => '',
                'end_time' => '',
                'total_time' => (string)$ext['cert_time'],
                'title' => (string)$ext['name'],
                'value1' => '',
                'value2' => '',
                'value3' => '',
                'value4' =>  (string)$ext['ext'],
                'extend_info' => array()
            );
        }

        $eduExp = $matcher->multi(array(
            array('one',self::getBlockRegex('教育经历')),
            array('all','/<tr>\s*<td[\s\S.]*?\"text_left\"\s*>(?<start_time>[\s\S.]*?)--(?<end_time>[\s\S.]*?)<\/td>\s*<td[\s\S.]*?>(?<school>[\s\S.]*?)<\/td>\s*<td[\s\S.]*?>(?<major>[\s\S.]*?)<\/td>\s*<td[\s\S.]*?>(?<academic>[\s\S.]*?)<\/td>\s*<\/tr>\s*(<tr>\s*<td\scolspan=\"4\"[\s\S.]*?>(?<detail>[\s\S.]*?)<\/td>\s*<\/tr>)?/m'),
        ));
        foreach($eduExp as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_EDUCATION,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => '',
                'title' => (string)$ext['school'],
                'value1' => (string)$ext['major'],
                'value2' => (string)$ext['academic'],
                'value3' => '',
                'value4' => (string)$ext['detail'],
                'extend_info' => array(),
            );
        }

        $trainExp = $matcher->multi(array(
            array('one','/培训经历[\s\S.]*?<table.*?>(?<train_data>[\s\S.]*?)<\/table>/m'),
            array('all','/<tr>\s*<td.*?>(?<start_time>[\s\S.]*?)--(?<end_time>[\s\S.]*?)：[\s\S]*?<\/td>\s*<td.*?>(?<name>.*?)<\/td>\s*<td.*?>(?<skill>.*?)<\/td>\s*<td.*?>(?<other>.*?)<\/td>\s*<\/tr>(\s*<tr>\s*<td\s*colspan=\"4\".*?>(?<detail>.*?)<\/td>\s*<\/tr>)?/m'),
        ));
        foreach($trainExp as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_TRAIN,
                'start_time' => (string)$ext['start_time'],
                'end_time' => (string)$ext['end_time'],
                'total_time' => '',
                'title' => (string)$ext['name'],
                'value1' => (string)$ext['skill'],
                'value2' => (string)$ext['other'],
                'value3' => '',
                'value4' => (string)$ext['detail'],
                'extend_info' => array(),
            );
        }


        $languageAbilities = $matcher->multi(array(
            array('one',self::getBlockRegex('语言能力')),
            array('all',$keyValueRegex),
        ));
        foreach($languageAbilities as $ext){
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_LANGUAGE,
                'start_time' => '',
                'end_time' => '',
                'total_time' => '',
                'title' => (string)$ext['name'],
                'value1' => '',
                'value2' => '',
                'value3' => '',
                'value4' => (string)$ext['value'],
                'extend_info' => array(),
            );
        }



        $otherInfo = $matcher->multi(array(
            array('one','/其他信息<\/td>\s*<\/tr>\s*<tr>[\s\S.]*?<\/tr>\s*<tr>[\s\S.]*?<\/tr>\s*<tr>\s*<td\salign=\"left\"\svalign=\"middle\">[\s\S.]*?<table.*?>(?<value>[\s\S.]*?)<\/table><\/td>\s*<\/tr>\s*<tr>/m'),
            array('all',$keyValueRegex),
        ));
        if (!is_array($otherInfo) || empty($otherInfo)) {
            //$otherInfo = Utils::matchOne($html,'/其他信息<\/td>\s*<\/tr>\s*<tr>[\s\S.]*?<\/tr>\s*<tr>[\s\S.]*?<\/tr>\s*<tr>\s*<td\salign=\"left\"\svalign=\"middle\">(?<other_info>[\s\S.]*?)<\/td>\s*<\/tr>\s*<tr>/m','other_info');
            $otherInfo = rtrim(trim(strip_tags($matcher->one(self::getBlockRegex('其他信息')), '<br>')));
            $resume['extend'][] = array(
                'type' => Constant::RESUME_EXTEND_TYPE_OTHER,
                'start_time' => '',
                'end_time' => '',
                'total_time' => '',
                'title' => '其他信息',
                'value1' => '',
                'value2' => '',
                'value3' => '',
                'value4' => (string)$otherInfo,
                'extend_info' => array(),
            );
        }else {
            foreach($otherInfo as $ext){
                $resume['extend'][] = array(
                    'type' => Constant::RESUME_EXTEND_TYPE_OTHER,
                    'start_time' => '',
                    'end_time' => '',
                    'total_time' => '',
                    'title' => (string)$ext['name'],
                    'value1' => '',
                    'value2' => '',
                    'value3' => '',
                    'value4' => (string)$ext['value'],
                    'extend_info' => array(),
                );
            }
        }
        return $resume;
    }

    /**
     * 获取简历块
     * @param $name
     * @return string
     */
    static protected function getBlockRegex($name)
    {
        return "/$name<\/td>\s*<\/tr>\s*<tr>\s*<td.*?>.*?<\/td>\s*<\/tr>\s*<tr>\s*<td.*?><\/td>\s*<\/tr>\s*<tr>\s*<td.*?>\s*<table.*?>(?<block>[\s\S.]*?)<\/table>/m";
    }
}