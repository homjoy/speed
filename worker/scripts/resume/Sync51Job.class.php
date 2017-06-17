<?php
namespace Worker\Scripts\Resume;
use Worker\Package\Common\ResumeHelper;
use Worker\Package\Utils\Downloader;
use Worker\Package\Extractor\Job51Extractor;

/**
 *
 * Class Sync51Job
 * @package Worker\Scripts\Resume
 */
class Sync51Job extends SyncBase{
    protected $downloader;
    protected $platform = '51job';
    protected $homeUrl = 'http://ehire.51job.com/Navigate.aspx';
    protected $loginUrl = 'http://ehire.51job.com/MainLogin.aspx';

    protected function _init()
    {
        $this->downloader = new Downloader(array(
            'referer' => $this->homeUrl,
            'downloadPath' => $this->downloadPath
        ));

        $this->downloader->setIsLogin(true);

        $self = $this;
        $this->downloader->checkLogin(function() use($self){
            $self->downloader->get($this->homeUrl);
            $self->downloader->get($this->homeUrl);
            $html = $self->downloader->rawContent();
            $regex = '/对不起，系统正忙，请稍候再试/m';
            if( preg_match($regex,$html,$matches) > 0 ){
                $this->log('Cookie失效，清空COOKIE重新登陆.');
                $self->downloader->setCookie('');
                return false;
            }

            $result = Job51Extractor::checkLogin($html);
            if(!$result){
                $this->log('用户未登录.');
                return false;
            }

            if(is_array($result)){
                $this->log('账号已通过其他浏览器登录，请检查是否有重复登陆。');
                $this->log("用户名：{$result['username']}");
                $this->log("电话：{$result['phone']}");
                $this->log("登陆时间：{$result['login_time']}");
                $this->log("登陆IP：{$result['login_ip']}");
                die();
            }

            $this->log('已经登陆，无需登陆');
            return true;
//            $regex = '/只记住会员名和用户名。.*?为了您的信息安全，请不要在网吧.*?或公用电脑上使用此功能/m';
//            if( preg_match($regex,$html,$matches) > 0 ){
//                $this->log('登陆失效，请重新登陆');
//                return false;
//            }else{
//                $this->log('已经登陆，无需登陆');
//                return true;
//            }
        });

        $this->downloader->login(function() use($self){
            $this->log('开始登陆51job');
            $crawler = $self->downloader->get($this->loginUrl);
            $nickname = '美丽说';
            $username = 'meilishuo';
            $password = 'meilishuo888';
            $values = $crawler->filter('form[id=form1]')->form()->getValues();
            $validateCode = '';
            //如果需要登陆验证码
            if(isset($values['txtCheckCodeCN'])){
                $this->log('登陆需要输入验证码');
                $validateCode = $this->retryGetVerifyCode(
                    'http://ehire.51job.com/CommonPage/RandomNumber.aspx?type=login&r='.(mt_rand() / mt_getrandmax())
                );
                $this->log("获取到验证码{$validateCode},开始尝试登陆账号.");
            }

            $postData = array(
                'ctmName'=>$nickname,
                'userName'=>$username,
                'password'=>$password,
                'checkCode'=> $validateCode,
                'oldAccessKey'=>$values['hidAccessKey'],
                'langtype'=>$values['hidLangType'],
                'isRememberMe'=>'false',
                'sc'=> $values['fksc'],
                'ec'=>$values['hidEhireGuid'],
                'returl'=>$values['hidRetUrl'],
            );

            //测试了必须添加referer，否则会直接跳转到登陆页面
            $self->downloader->setReferer($this->loginUrl);
            $crawler = $self->downloader->post('https://ehirelogin.51job.com/Member/UserLogin.aspx',$postData);
            $html = $self->downloader->rawContent();
            $result = Job51Extractor::checkLogin($html);
            if(!$result){
                $this->log('登陆失败!同步结束.');
                die();
            }

            if(is_array($result)){
                $this->log('账号已通过其他浏览器登录，请检查是否有重复登陆。');
                $this->log("用户名：{$result['username']}");
                $this->log("电话：{$result['phone']}");
                $this->log("登陆时间：{$result['login_time']}");
                $this->log("登陆IP：{$result['login_ip']}");


                //强踢下线。
                $form = $crawler->filter('form[id=form1]')->form(array(
                    '__EVENTTARGET'=>'gvOnLineUser',
                    '__EVENTARGUMENT'=>'KickOut$0'
                ));
                $this->downloader->submit($form);
                //die();
            }

            $this->log('登陆成功.');
            return ;
        });
    }

    public function __destruct()
    {
        //退出登陆
        //不退出会一直处于登陆状态，
        //下次必须踢掉才能正常登陆
        //$this->downloader->get('http://ehire.51job.com/LoginOut.aspx');
    }


    public function run()
    {
        $html = $this->downloadByResumeId(6647421664,true);
        $resume = Job51Extractor::resume($html);

        $poolResume = \Frame\Speed\Lib\Api::atom('recruit/resumeGet',array(
            'phone'=>$resume['phone'],'email'=>$resume['email'],'full'=>1
        ));
        //合并简历
        $finalResume = ResumeHelper::merge($poolResume,$resume);
        $ret = \Frame\Speed\Lib\Api::atom('recruit/resumeSave',$finalResume);


        var_dump($ret);


//        $this->testDownloaded();

        $this->response->setBody('51job 简历同步结束!');
    }


    /**
     * 下载指定的简历页面
     * @param $id
     * @return string
     */
    private function downloadByResumeId($id,$cached=false)
    {
        if(empty($id)){
            return '';
        }
        $filename = 'ResumeViewFolder_'.$id.'.html';
        if($cached){
            return $this->downloader->read($filename);
        }

        $url = 'http://ehire.51job.com/Candidate/ResumeViewFolder.aspx?hidSeqID='.$id.'&hidFolder=EMP';
        $this->downloader->get($url);
        $this->downloader->save($filename);
        return $this->downloader->rawContent();
    }

    /**
     * 下载指定职位下的简历列表页面
     * @param $jobId
     * @return string
     */
    private function downloadListByJob($jobId)
    {
        if(empty($jobId)){
            return '';
        }

        $url = 'http://ehire.51job.com/Inbox/InboxViewEngine.aspx?bigCode=tp&code=0202&linkType=readResume&strIsHis=Y&JobID='.$jobId;
        $this->downloader->get($url);
        $html = $this->downloader->rawContent();
        $filename = 'InboxViewEngine_'.$jobId.'.html';
        $this->downloader->save($filename,$html);
        return $html;
    }
}