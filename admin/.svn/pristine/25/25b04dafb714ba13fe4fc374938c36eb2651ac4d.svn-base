<?php

namespace Admin\Package\Meeting;

/**
 * 会议室通用方法
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class Meeting extends \Admin\Package\Common\BasePackage {

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
     * 获取会议室的列表
     * @param array $params
     *
     * @return bool
     */
    public  function getMeetingList($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/meeting_room_list', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 获取一个会议室的信息
     * @param array $params
     *
     * @return bool
     */
    public function meetingRoomGet($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/meeting_room_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 更新会议室信息
     * @param array $params
     *
     * @return bool
     */
    public function meetingRoomUpdate($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/meeting_room_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 创建会议室
     * @param array $params
     *
     * @return bool
     */
    public function meetingRoomCreate($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/meeting_room_create', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
