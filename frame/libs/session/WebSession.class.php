<?php
namespace Libs\Session;

class WebSession extends Session {
    /**
     * 普通登录态的session_key
     * @return string
     */
    public function getTicket() {
        $ticket = "";
        if(isset($_COOKIE['app_access_token'])) {
            $ticket = trim($_COOKIE['app_access_token']);
            $ticket = "Mob:Session:AccessToken:".$ticket;
            $this->setLoginFrom("mob-wap");
        }
        else if(isset($_COOKIE['access_token'])) {
            $ticket = trim($_COOKIE['access_token']);
            $ticket = "Mob:Session:AccessToken:".$ticket;
            $this->setLoginFrom("mob-wap");
        }
        else if(isset($_COOKIE['santorini_mm'])) {
            $ticket = trim($_COOKIE['santorini_mm']);
            $this->setLoginFrom("pc");
        }
        return $ticket;
    }

    /**
     * 强登录态校验
     * @return string
     */
    public function getSTicket() {
        return "";
    }
}