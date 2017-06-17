<?php
namespace Joint\Modules\Itserver;

use Joint\Modules\Common\BaseModule;
use Joint\Package\Common\Response;
use Pixie\Exception;

/**
 * 邮件组管理
 * Class EmailCreate
 *
 * @package Joint\Modules\Itserver
 */
class EmailGroupManage extends BaseModule
{
	protected $errors = null;
	private $params = null;
	const EMAIL_URL = 'http://yz.it.api01.meiliworks.com/apis/mail/core_api_v1.php';

	public function run()
	{

		$this->_init();
		//1. 去邮件系统查询一下，这个email 是否已经存在

		//创建用户
		$create_params = array(
			'act' => $this->params['act'],
			'u'   => $this->params['u'],
		);
		if (!empty($this->params['forwardmaillist'])) {
			$data['forwardmaillist'] = $this->params['forwardmaillist'];
		} else {
			$data = array();
		}
		$create_return = $this->pushMailUserList($create_params,$data);
		$return = Response::gen_success($create_return);
		$this->app->response->setBody($return);

	}

	/**
	 * 获取参数
	 *
	 * @return bool
	 */
	private function _init()
	{

		$this->rules = array(
			'act'             => array(
				'required'   => true,
				'allowEmpty' => false,
				'type'       => 'string',

			),
			'u'               => array(
				'required'   => true,
				'allowEmpty' => false,
				'type'       => 'string',

			),
			'forwardmaillist' => array(
				'type' => 'string',

			),


		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();

		return true;
	}

	/**
	 * 推送邮件列表的相关数据
	 *
	 * @param type $params
	 *
	 * @data type array 需要post的数据
	 * @return type
	 *
	 */
	public function pushMailUserList($params = array(), $data = array())
	{
		$post_url = self::EMAIL_URL . '';
		if (empty($params['act'])) {
			return false;
		}
		$seckey = $this->checkKey($params['act']);
		$params['seckey'] = $seckey;
		$post_url = $post_url . '?' . http_build_query($params);
		$curl_obj = new \Libs\Sphinx\curl;
		$ret = $curl_obj->post($post_url, http_build_query($data), false);
		$body = json_decode($ret['body'], true);

		return $body;
	}

	/**
	 * 获取加密数据
	 *
	 * @param $act
	 *
	 * @return string
	 */
	public function checkKey($act)
	{
		$ts = time();
		$sharedkey = "CoreMail2015";
		$nseckey = $sharedkey . $act . "$ts";
		$seckey = md5($nseckey);

		return $seckey;
	}

}
