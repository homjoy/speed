<?php
namespace Worker\Scripts\Resume;
use Worker\Package\Common\ResumeHelper;
use Worker\Package\Utils\Downloader;
use Worker\Package\Extractor\ZhaoPinExtractor;

/**
 * Class SyncZhaoPin
 * @package Worker\Scripts\Resume
 */
class SyncZhaoPin extends SyncBase{

    protected $downloader;
    protected $platform = 'zhaopin';
    protected $debug = true;

    protected function _init()
    {
        //$this->downloadPath = realpath(APP_PATH .'/tmp/resumes/'.$this->platform);
        $this->downloader = new Downloader(array(
            'referer' => '',
            'downloadPath' =>$this->downloadPath
        ));

        $self = $this;

        $this->downloader->checkLogin(function() use($self){
            $self->downloader->get('http://rd2.zhaopin.com/s/homepage.asp');
            $html = $self->downloader->rawContent();
            $regex = '/<span\s*?class=\"compId\">公司编号：38900781<\/span>/m';
            if( preg_match($regex,$html,$matches) > 0 ){
                $this->log('已经登陆，无需登陆');
                return true;
            }else{
                $this->log('未登录系统，请登录.');
                return false;
            }
        });

        $this->downloader->login(function() use($self){
            $this->log('开始登陆智联招聘');
            $validateCode = $this->retryGetVerifyCode(
                'http://rd2.zhaopin.com/s/loginmgr/picturetimestamp.asp?t='.(time()*1000)
            );
            $this->log("获取到验证码{$validateCode},开始尝试登陆账号.");
            $crawler = $self->downloader->get('http://rd2.zhaopin.com/portal/myrd/regnew.asp?za=2');
            $username = 'gxhp38900781';
            $password = 'meilishuo@2015';
            $form = $crawler->filter('form[id=form1]')->form(array(
                'username'=>$username,
                'password'=>$password,
                'Validate'=>$validateCode,
            ));

            //提交登陆表单
            $crawler = $self->downloader->submit($form);
            if(!ZhaoPinExtractor::checkLogin($self->downloader->rawContent())){
                $this->log('登陆失败!同步结束.');
                die();
            }

            $this->log('登陆成功，选择登陆机构.');
            $this->downloader->get('http://rd2.zhaopin.com/s/loginmgr/loginpoint.asp?id=38900781&BkUrl=&deplogincount=1');
            return $self->downloader->rawContent();
        });
    }


    public function run()
    {
        $html = $this->downloadResumePreview(array(
                'resume_id'=>5050147410,
                'link'=>'http://rd.zhaopin.com/resumepreview/resume/viewone/1/5050147410?resume=5050147410&star=&companyid=38900781')
        );
        $resume = ZhaoPinExtractor::resume($html);

        $poolResume = \Frame\Speed\Lib\Api::atom('recruit/resumeGet',array(
            'phone'=>$resume['phone'],'email'=>$resume['email'],'full'=>1
        ));
        //合并简历
        $finalResume = ResumeHelper::merge($poolResume,$resume);
        $ret = \Frame\Speed\Lib\Api::atom('recruit/resumeSave',$finalResume);


        //自动测试已经下载的简历内容提取
        //$this->testDownloaded();

        //$this->syncResumeList();
        $this->log('智联招聘 简历同步结束!');
    }


    /**
     * 同步多少时间内的简历，以小时为单位
     * @param int $hour
     */
    public function syncResumeList($hour = 1)
    {
        //提取职位列表
        $this->log('开始同步简历列表.');
        $positionList = ZhaoPinExtractor::positionList($this->downloadPositionManage(1));
        $this->log($positionList);

//        $list = array(
//            array(
//                'position_id'=>156747694,
//                'title'=>'PHP高级程序员',
//                'status'=>3,
//                'is_feedback'=>0,
//            )
//        );
        //获取每个职位下投递的简历
        foreach($positionList as $position){
            //提取职位下的简历列表
            $resumeList = ZhaoPinExtractor::positionResumeList($this->downloadResumeByPosition($position));
            $this->log($resumeList);
            //提取简历
            foreach($resumeList as $resume){
                $postTime = strtotime($resume['post_time']);
                //TODO: 获取上一次最新投递的时间，然后检测投递时间是否过早，以便放弃
                //$hour小时以内投递的
                if($postTime > (time() - 3600*$hour) ){
                    $html = $this->downloadResumePreview($resume);
                    $resume = ZhaoPinExtractor::resume($html);
                    //Utils::dump($resume);
                    Resume::getInstance()->save($resume);
                }
            }
        }
    }

    /**
     * 下载简历
     * @param $resume
     * @return string
     */
    private function downloadResumePreview($resume){
        if(empty($resume)){
            return '';
        }

        $filename = '/resume_'.$resume['resume_id'].'.html';
        $this->downloader->get($resume['link']);
        $html = $this->downloader->rawContent();
        $this->downloader->save($filename,$html);
        return $html;
    }

    /**
     * 下载职位管理列表
     * @param int $page
     * @return array
     */
    private function downloadPositionManage($page = 1)
    {
        if($page == 1){
            $this->downloader->get('http://rd2.zhaopin.com/s/vacainfo/PositionManage.asp?ok=1');
            return $this->downloader->rawContent();
        }

        /**
         * 提交分页表单
         */
        $crawler = $this->downloader->get('http://rd2.zhaopin.com/s/vacainfo/PositionManage.asp');
        $form = $crawler->filter('form[name=managedVacancyForm]')->form(array('PageNo'=>$page));
        $crawler = $this->downloader->submit($form);
        return $this->downloader->rawContent();
    }


    /**
     * 下载职位下的简历列表
     * @param $job
     * @return string
     */
    private function downloadResumeByPosition($job)
    {
        if(empty($job)){
            return '';
        }

        $url = "http://rd2.zhaopin.com/rdapply/ResumeByPosition?SF_1_1_46=0".
            "&SF_1_1_44=" .$job['position_id']
            ."&JobTitle=".$job['title']
            ."&JobStatus=".$job['status']
            ."&reCount=0"
            ."&FeedBack=".$job['is_feedback'];

        $this->downloader->get($url,true);
        $this->downloader->save('ResumeByPosition_'.$job['position_id'].'.html');
        return $this->downloader->rawContent();
    }
}