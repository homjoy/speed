<?php
namespace Libs\Session;

use \Libs\Cache\Memcache;

class Session {
    /**
     * session struct
     * user_id
     * gender
     * login_type
     * login_from
     * @var array
     */
    private $session=array();
    private $ticket = "";
    private $s_ticket = "";
    private $login_from = 0;
    public function __construct() {
        $this->loadSession();
    }

    private function loadSession() {
        $this->ticket = $this->getTicket();
        $this->s_ticket = $this->getSTicket();

        if($this->checkLogin()) {
            return;
        }
        if($this->checkSLogin()) {
            return;
        }
    }

    public function checkLogin() {
        if(empty($this->ticket)) {
            return FALSE;
        }
        $this->session = Memcache::instance()->get($this->ticket);
        if(!empty($this->session)) {
            $this->session && $this->session['session_data'] && $this->session = $this->session['session_data'];

            if($this->session && $this->session['user_id']) {
                $this->session['login_type'] = 1;
                $this->session['login_from'] = $this->login_from;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function checkSLogin() {
        if(empty($this->s_ticket)) {
            return FALSE;
        }
        $this->session = Memcache::instance()->get($this->s_ticket);
        if(!empty($this->session)) {
            if($this->session && $this->session['user_id']) {
                $this->session['login_type'] = 2;
                $this->session['login_from'] = $this->login_from;
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 普通登录态的session_key
     * @return string
     */
    public function getTicket() {
        return "";
    }

    /**
     * 强登录态校验
     * @return string
     */
    public function getSTicket() {
        return "";
    }

    public function __get($arg) {
        if(isset($this->session[$arg])) {
            return $this->session[$arg];
        }
        else if($arg=="session") {
            return $this->session;
        }

        return FALSE;
    }

    public function setLoginFrom($from) {
        $this->login_from = $from;
    }
}