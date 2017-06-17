<?php

namespace Admin\Package\Meeting;

/**
 * 获取room的提供的服务
 * @package Admin\Package\Workflow
 * @author guojiezhu@meilishuo.com
 * @since 2015-11-05
 */

class RoomService extends \Admin\Package\Common\BasePackage {

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
     * 获取
     * @param array $params
     *
     * @return bool
     */
    public  function roomServiceAll($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/room_service_all', $params);
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
     * 获取当前room的 所有的规则
     * @param array $params
     *
     * @return bool
     */
    public function roomServiceRuleGet($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/room_service_rule_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
    /**
     * 获取当前room的 规则内容
     * @param array $params
     *
     * @return bool
     */
    public function roomServiceGet($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/room_service_get', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 修改会议室的服务项目
     * @param array $params
     *
     * @return bool
     *
     */
    public function roomServiceRuleUpdate($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/room_service_rule_update', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }

    /**
     * 删除服务
     * @param array $params
     *
     * @return bool
     */
    public function roomServiceRuleDelete($params = array()){
        $ret = self::getClient()->call('atom', 'meeting/room_service_rule_delete', $params);
        $ret = self::parseRemoteData($ret);
        return $ret;
    }
}
