<?php
namespace Admin\Modules\Message_send;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Message_send\MessageSend;
use Admin\Package\Account\UserInfo;
use Admin\Package\Account\PersonalInfo;
/**
 * 消息发送后台
 * Class AjaxMessageSend
 *
 * @package Admin\Modules\Messagesend
 */
set_time_limit(60);
class AjaxMessageSend extends BaseModule
{

	protected $errors = null;
	private $params = null;
	public static $VIEW_SWITCH_JSON = true;
	//返回数据
	protected $return_info = array();

	protected $page_size = 200;

	public function run()
	{

		$this->_init();
		if ($this->post()->hasError()) {
			$return = Response::gen_error(10001, '', $this->post()->getErrors());

			return $this->app->response->setBody($return);
		}
		$send_user_list = $this->params['send_user'];
		unset($this->params['send_user']);
		$this->params['op_user_id'] = $this->user['id'];
        $msg_id = array();
        if(empty($this->params['msg_id'])) {//添加

            $msg_id = MessageSend::getInstance()->MessageListCreate($this->params);


        }else{//更新
            $msg_id = MessageSend::getInstance()->MessageListUpdate($this->params);
            if($msg_id){
                //删除原有msg_user_list 根据msg_id 在此我就不拿出msg_id 费事
                $msg_id = $this->params['msg_id'];
                $info = MessageSend::getInstance()->getMessageUserList(
                    array(
                        'msg_id'=>isset($this->params['msg_id'])?$this->params['msg_id']:0
                    )
                );
                if($info){//有信息 则要删除
                    if(!MessageSend::getInstance()->MessageUserListBatchDelete($this->params)){
                        $return  = Response::gen_error(50003,'消息队列更新成功,但是更改失败');
                        return    $this->app->response->setBody($return);
                    }
                }

            }

        }
        if (empty($msg_id)) {
            $return = Response::gen_error(10001, '消息队列创建或更新失败');
            return $this->app->response->setBody($return);
        }
		//用户输入的信息
		if(in_array($this->params['send_object'],array(4,5,6)) && empty($send_user_list)){
			$return = Response::gen_error(10001, '输入的发送对象为空');
			return $this->app->response->setBody($return);
		}
		//输入信息的解析
		if(in_array($this->params['send_object'],array(4,5,6)) && !empty($send_user_list) ){
			//非法的手机号码
			$illegal_array = array();
			$send_user = str_replace(array("\r\n", "\r", "\n", " ", " "), "#", $send_user_list);
			$send_user = explode("#", $send_user);
			$send_user = array_unique($send_user);
			foreach ($send_user as $user_key => &$send_user_value) {
				$send_user_value = trim($send_user_value);
				if (empty($send_user_value)) {
					unset($send_user[$user_key]);
				}
				switch($this->params['send_object']){
					case 4: //短信校验
						if(!$this->_check_mobile($send_user_value)){
							$illegal_array[$send_user_value] = '请输入正确手机号码';
							unset($send_user[$user_key]);
						}
						break;
					case 5: //邮箱校验
						$send_user_value =$this -> _check_mail($send_user_value);
						break;
				}
			}
			if(!empty($illegal_array)){
				foreach($illegal_array as $ill_key=>$ill_value){
					$show_ill_message = $ill_key .':'. $ill_value.';';
				}
				$return = Response::gen_error(50001,$show_ill_message);
				return $this->app->response->setBody($return);
			}
			unset($send_user_value);
		}
		$params = array(
				'msg_id'  => $msg_id,
				'channel' => $this->params['channel']
		);
		//做判断处理
		switch ($this->params['send_object']) {
			case 1: //按部门发送
				break;
			case 2: //在职员工
				$return_info = $this->_sendMsgOrderUserStatus($params,'on');

				break;
			case 3: //离职员工
				$return_info = $this->_sendMsgOrderUserStatus($params,'off');
				break;
			case 4: //手机号码
				$params['send_user'] = $send_user;
				$return_info = $this->_sendMsgOrderPhone($params);
				break;
			case 5: //邮箱前缀
				$params['send_user'] = $send_user;
				$return_info = $this->_sendMsgOrderMail($params);
				break;
//			case 6: //员工工号
//				break;

		}
		$return = array();
		if(empty($return_info)){
			$return = Response::gen_success(array());
		}else{
			foreach($return_info as $key=>$value){
				$show_message = $key .':'. $value.';';
			}
			$return = Response::gen_error(50001,$show_message);
		}
		return $this->app->response->setBody($return);

	}

	/**
	 * 按照部门发送
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _sendMsgOrderDepart($params = array())
	{
		return array();
	}

	/**
	 * 根据在职或者离职发送信息
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _sendMsgOrderUserStatus($params = array(),$status='on')
	{
		if (empty($params)) {
			return array();
		}
		//1.查询在职员工信息
		if($status =='on') {
			$user_params = array(
					'status' => '1,3',
			);
		}else {
			$user_params = array(
					'status' => '2',
			);
		}
		//统计出人数,然后进行开发
		$user_params_count = $user_params;
		$user_params_count['count'] = 1;
		$online_user_count = UserInfo::getInstance()->getUserInfo($user_params_count);
		if (intval($online_user_count) <= 0) {
			return array();
		}
		$page_nums = ceil(intval($online_user_count) / $this->page_size);
		//返回的结果数据
		$return = array();
		for ($i = 0; $i < $page_nums; $i++) {
			$user_params['offset'] = $i*$this->page_size;
			$user_params['limit'] = $this->page_size;

			$online_user_list = UserInfo::getInstance()->getUserInfo($user_params);
			if (empty($online_user_list)) {
				continue;
			}
			//读取消息
			if ($params['channel'] == 2) {
				$user_id_array = array_keys($online_user_list);
				//查询用户的手机号码
				$user_id_string = implode(',',$user_id_array);
				$person_info = PersonalInfo::getInstance()->getPersonalInfo(array('user_id' => $user_id_string,'limit' => $this->page_size));
				foreach ($online_user_list as $user_key => $user_list) {
					$send_phone = !empty($person_info[$user_key]['mobile']) ? trim($person_info[$user_key]['mobile']) : '';
					if(empty($send_phone) || !$this->_check_mobile($send_phone)) {
						$return[$user_list['name_cn']] = $send_phone.' 手机号码不正确';
						continue;
					};
					//手机信息发送,去读取手机号码
					$message_user_list = array(
							'msg_id'  => $params['msg_id'],
							'name_cn' => $user_list['name_cn'],
							'to_id'   => $user_list['user_id'],
							'mail'    => $user_list['mail'],
							'phone'   => $send_phone,

					);
					//直接获取用户数据,存储到用户表
					$ret = MessageSend::getInstance()->MessageUserListBatchCreate($message_user_list);
					if($ret <=0){
						$return[$user_list['name_cn']] = '存入消息发送详情失败';
					}
				}

			} else {
				foreach ($online_user_list as $user_list) {
					//手机信息发送,去读取手机号码
					$message_user_list = array(
							'msg_id'  => $params['msg_id'],
							'name_cn' => $user_list['name_cn'],
							'to_id'   => $user_list['user_id'],
							'mail'    => $user_list['mail'],
							'phone'	  => ''
					);
					//直接获取用户数据,存储到用户表
					$ret = MessageSend::getInstance()->MessageUserListCreate($message_user_list);
					if($ret <=0){
						$return[$user_list['name_cn']] = '存入消息发送详情失败';
					}
				}
			}



		}

		return $return;
	}



	/**
	 * 根据手机号码发送信息,只能发送短信
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _sendMsgOrderPhone($params = array())
	{
		if(empty($params)) return array();
		//判断,根据手机号码 要发送的什么数据
		$return = array();
		foreach($params['send_user'] as $value){
			$user_detail_info = array(
					'msg_id'  => $params['msg_id'],
					'name_cn' => '',
					'to_id'   => '',
					'mail'    => '',
					'phone'	  => $value
			);
			$ret = MessageSend::getInstance()->MessageUserListCreate($user_detail_info);
			if($ret <=0){
				$return[$value] = '存入消息发送详情失败';
			}
		}
		return $return;
	}

	/**
	 * 根据邮箱发送数据(可发送邮件和短信)
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _sendMsgOrderMail($params = array())
	{
		if(empty($params)|| empty($params['send_user'])){
			return array();
		}
		$return = array();
		//发送渠道 1 邮件,直接将数据插入到数据库,不去获取用户信息;2 短信 需要去获取用户的信息,然后获取短信
		if($params['channel'] == 2){
			foreach($params['send_user'] as $send_value) {
				$mail_info = explode('@',$send_value);
				$mail_string = current($mail_info);
				$user_info = UserInfo::getInstance()->getUserInfo(array('mail' => $mail_string));
				if(empty($user_info)){
					$return[$send_value] = '用户不存在';
					continue;
				}
				$user_info = current($user_info);
				$phone_info = PersonalInfo::getInstance()->getPersonalInfo(array('user_id'=>$user_info['user_id']));
				if(empty($phone_info)){
					$return[$send_value] = '用户手机号不存在';
					continue;
				}
				$phone_info = current($phone_info);
				$user_detail_info = array(
						'msg_id'  => $params['msg_id'],
						'name_cn' => $user_info['name_cn'],
						'to_id'   => $user_info['user_id'],
						'mail'    => $send_value,
						'phone'   => $phone_info['mobile']
				);
				$ret = MessageSend::getInstance()->MessageUserListCreate($user_detail_info);
				if ($ret <= 0) {
					$return[$send_value] = '存入消息发送详情失败';
				}
			}
		}else{
			foreach($params['send_user'] as $send_value) {
				$user_detail_info = array(
						'msg_id'  => $params['msg_id'],
						'name_cn' => '',
						'to_id'   => '',
						'mail'    => $send_value,
						'phone'   => ''
				);
				$ret = MessageSend::getInstance()->MessageUserListCreate($user_detail_info);
				if ($ret <= 0) {
					$return[$send_value] = '存入消息发送详情失败';
				}
			}
		}
		return $return;
	}

	/**
	 * 根据员工编号发送信息
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _sendMsgOrderStaffId($params = array())
	{
		return array();
	}

	/**
	 * 检测手机号码
	 * @param $mobile
	 *
	 * @return mixed
	 */
	protected  function _check_mobile($mobile){
		if(strlen($mobile)!=11) return false;
		return (preg_match("/(?:13|14|17|18|15)[0-9]{9}$/",$mobile))?true:false;
	}
	/**
	 *  检测mail 是否符合规则
	 * @param $mail
	 *
	 * @return mixedd
	 */
	protected  function _check_mail($mail){
		if(!preg_match('#@#',$mail)){
			return $mail.'@meilishuo.com';
		}
		return $mail;

	}

	//获取参数
	private function _init()
	{

		$this->rules = array(
			'send_object' => array(
				'required'   => true,
				'allowEmpty' => false,
				'type'       => 'integer',
			),
			'send_user'   => array(
				'type' => 'string',
			),
			'channel'     => array(
				'type'       => 'integer',
				'required'   => true,
				'allowEmpty' => false,

			),
			'title'       => array(
				'type'       => 'string',
				'required'   => true,
				'allowEmpty' => false,

			),
			'content'     => array(
				'type'       => 'string',
				'required'   => true,
				'allowEmpty' => false,

			),
			'send_at'     => array(
				'type'       => 'string',
				'required'   => true,
				'allowEmpty' => false,

			),
			'template_id' => array(
				'type'       => 'integer',
				'required'   => true,
				'allowEmpty' => false,

			),
			'weights'     => array(
				'type'       => 'integer',
				'required'   => true,
				'allowEmpty' => false,

			),
			'status'      => array(

				'type'    => 'integer',
				'default' => 0
			),
			'send_status' => array(

				'type'    => 'integer',
				'default' => 0
			),
            'msg_id' => array(

                'type'    => 'integer',
                'default' => 0
            ),

		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();

	}

}