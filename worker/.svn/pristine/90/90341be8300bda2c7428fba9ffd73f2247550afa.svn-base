<?php
namespace Worker\Package\Utils;

/**
 * 邮件提取工具类
 *
 * Class MailFetcher
 * @package Worker\Package\Common
 */
class MailFetcher{
    protected $username = '';
    protected $password = '';
    protected $attachDir =  '';
    protected $mailer = null;

    public function __construct($username,$password,$attachDir = '')
    {
        $this->username = $username;
        $this->password = $password;


        if(!empty($attachDir) && file_exists($attachDir)){
            $this->attachDir = $attachDir;
        }else{
            $attachDir = APP_PATH . '/tmp/attachments';
            if(!file_exists($attachDir)){
                mkdir($attachDir);
            }
            $this->attachDir = $attachDir;
        }
    }

    /**
     *
     */
    protected function connect()
    {
        try{
            $this->mailer = new \ImapMailbox('{pop.qq.com:995/pop3/ssl}INBOX', $this->username, $this->password, $this->attachDir, 'utf-8');
        }catch (\Exception $e){
            die($e->getMessage());
        }
    }

    protected function get($exclude = array())
    {
        $this->connect();
        $mailIds = $this->mailer->searchMailBox('ALL');
        if(empty($mailsIds)) {
            return array();
        }

        //排除掉已经抓取过的邮件
        $mailIds = array_diff($mailIds,$exclude);


        $mails = array();
        foreach($mailIds as $mId){
            $mails[] = $this->mailer->getMail($mId);
        }

        return $mails;
    }
}