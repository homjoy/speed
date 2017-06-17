<?php

namespace Admin\Package\Message_send;

/**
 * 会议室通用方法
 *
 * @package Admin\Package\Workflow
 * @author  guojiezhu@meilishuo.com
 * @since   2015-11-05
 */

class MessageSend extends \Admin\Package\Common\BasePackage
{

	private static $instance = null;
	//发送对象
	public static $sendObject = array( 2=>'在职员工',3=>'离职员工',4=>'手机号(仅支持发送短信)',5=>'邮箱'); //1=>'按部门',
	//
	public static $weights = array(
		'10' => '非常紧急',30=> '紧急',50=>'不紧急'
	);
	public static $status = array(
			'0' => '无效',1=> '有效'
	);
	public static $sendStatus = array(
			'0' => '未发送',1=> '发送中',9=>'发送失败',10=>'发送成功'
	);
	private function __construct() { }

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();

		}

		return self::$instance;
	}

	/**
	 * 获取消息列表
	 * @param array $params
	 *
	 * @return bool
	 */
	public function getMessageList($params = array()){
		$ret = $this->getClient()->call('atom', 'message_send/message_list_get', $params);
       $ret = self::parseRemoteData($ret);

		return $ret;
	}

    /**
     * 获取消息用户列表
     * @param array $params
     *
     * @return bool
     */
    public function getMessageUserList($params = array()){
        $ret = $this->getClient()->call('atom', 'message_send/message_user_list_get', $params);
        $ret = self::parseRemoteData($ret);

        return $ret;
    }

	/**
	 * 创建信息发送
	 *
	 * @param array $params
	 *
	 * @return bool
	 */
	public function MessageListCreate($params = array())
	{
		$ret = $this->getClient()->call('atom', 'message_send/message_list_add', $params);
		$ret = self::parseRemoteData($ret);

		return $ret;
	}





    /**
	 * 创建发送人员的列表
	 *
	 * @param array $params
	 *
	 * @return bool
	 */
	public function MessageUserListCreate($params = array())
	{
		$ret = $this->getClient()->call('atom', 'message_send/message_user_list_add', $params);
		$ret = self::parseRemoteData($ret);

		return $ret;
	}


	/**
	 * 创建发送人员的列表
	 *
	 * @param array $params
	 *
	 * @return bool
	 */
	public function MessageUserListBatchCreate($params = array())
	{
		$ret = $this->getClient()->call('atom', 'message_send/message_user_list_add', $params);
		$ret = self::parseRemoteData($ret);

		return $ret;
	}

    /**
     * 更新发送人员的列表
     *
     * @param array $params
     *
     * @return bool
     */
    public function MessageListUpdate($params = array())
    {
        $ret = $this->getClient()->call('atom', 'message_send/message_list_update', $params);

        $ret = self::parseRemoteData($ret);

        return $ret;
    }
    /**
     * 删除发送人员的列表
     *
     * @param array $params
     *
     * @return bool
     */
    public function MessageUserListBatchDelete($params = array())
    {
        $ret = $this->getClient()->call('atom', 'message_send/message_user_list_delete_by_msg_id', $params);
        $ret = self::parseRemoteData($ret);

        return $ret;
    }



}
