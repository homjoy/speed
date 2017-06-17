<?php
namespace Atom\Modules\Core;

use Atom\Package\Core\UserAccount;
use Libs\Util\Format;
use Atom\Package\Common\Response;

/**
 * 获取用户的登陆
 * @author guojiezhu@meilishuo.com   edit  hongzhou@meilishuo.com
 * @since 2015-12-28
 */

class GetUserAccount extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;

	public function run() {
		$this->sample  =  UserAccount::model()->getFields();
		$queryParams =array();
        $this->_init();
		if(!empty($this->params['update_time'])){
			$queryParams['update_time'] = date('Y-m-d H:i:s',strtotime($this->params['update_time']));
		}
		if(!empty($this->params['end_time'])){
			$queryParams['end_time'] = date('Y-m-d H:i:s',strtotime($this->params['end_time']));
		}
		if(!empty($this->params['user_id'])){
			$queryParams['user_id'] = $this->params['user_id'];
		}
		if(!empty($this->params['login_name'])){
			$queryParams['login_name'] = $this->params['login_name'];
		}

		if(!empty($this->params['status'])){
			$queryParams['status'] = $this->params['status'];
		}
        if(!empty($this->params['account_type'])){
            $queryParams['account_type'] = $this->params['account_type'];
        }

		if(!empty($this->params['count'])){
			$queryParams['count'] = $this->params['count'];
		}
		if(!empty($this->params['match'])){
			$queryParams['match'] = $this->params['match'];
		}
        if(!empty($this->params['all'])){
            $queryParams['all'] = $this->params['all'];
        }
        $result = UserAccount::model()->getData(
            $queryParams,
            $this->params['offset'],
            $this->params['limit']

        );
		if ($result === FALSE) {
			$return = Response::gen_error(50001);
		}else{
			if(!empty($queryParams['count']) ){

				$return =  Response::gen_success($result);
			}else{
				$return =  Response::gen_success(Format::outputData($result, $this->sample, TRUE));
			}
		}

		$this->app->response->setBody($return);
	}

	/**
	 * 参数初始化
	 */
	protected function _init(){
		$this->rules = array(
			'user_id'=>array(
				'type'=>'multiId',
			),
			'login_name' => array(
				'type' => 'string',
			),
			'status' => array(
				'type' => 'multiId',
			),
			'update_time' => array(
				'type' => 'string',
			),
			'end_time' => array(
				'type' => 'string',
			),
			'account_type' => array(
				'type' => 'multiId',
			),
			'count' => array(   //当为1时获取限定条件之后的总行数
				'type' => 'integer',
			),
			'all' => array(   //当为1时获取所有数据
				'type' => 'integer',
			),
			'offset' => array(
					'type' => 'integer',
					'default'=>0,
			),
			'limit' => array(
					'type' => 'integer',
					'default'=>100,
			),
		);
		$this->params   = $this->query()->safe();
	}

}
