<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserToken;

/**
 * 获取token信息列表
 * @author minggeng@meilishuo.com
 * @since 2015-06-02
 */

class UserTokenList extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();
		$this->sample   = UserToken::model()->getFields();

		$queryParams = $this->query()->params_filter($this->params);

        $result = UserToken::model()->getList($queryParams);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }
    	$this->app->response->setBody($return);
	}

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
            'type'			=> array(
                'required'	=> true,
                'type'		=> 'string',
                'maxLength'	=> 50,
            ),
            'token' 		=> array(
                'required'	=> true,
                'type'		=> 'string',
                'maxLength'	=> 32,
            ),
        );
        $this->params     = $this->query()->safe();
        $this->params['user_id'] = $this->app->currentUser['user_id'];
    }
}
