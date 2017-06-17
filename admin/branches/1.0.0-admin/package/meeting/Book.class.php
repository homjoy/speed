<?php

namespace Admin\Package\Meeting;

/**
 * 会议室通用方法
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class Book extends \Admin\Package\Common\BasePackage {

    private static $instance = null;
    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$instance)){
            self::$instance = new self();

        }
        return self::$instance;
    }


    /**
     *
     * @param array $params
     *
     * @return bool
     */
    public  function getBooks($params = array()){
        $ret =  $this->getClient()->call('atom', 'book/get_books',$params);

        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     *
     * @param array $params
     *
     * @return bool
     */
    public function deleteBook($params = array()){
        $ret = self::getClient()->call('atom', 'book/meeting_book_delete', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     *
     * @param array $params
     *
     * @return bool
     */
    public function getRoomBooks($params = array()){
        $ret =$this->getClient()->call('atom', 'book/get_room_books', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }


}
