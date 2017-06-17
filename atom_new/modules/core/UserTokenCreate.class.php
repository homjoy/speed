<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\UserToken;

/**
 * 新建token信息
 * @author minggeng@meilishuo.com
 * @since 2015-06-01
 */

class UserTokenCreate extends \Atom\Modules\Common\BaseModule {

    protected $data = array();

	public function run() {
        $this->_init();

        $result = UserToken::model()->insertOrUpdate($this->data);

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
            $this->data['id'] = $result;
        	$return = $result;
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
            'user_id' 		=> array(
                'required'	=> true,
                'type'		=> 'integer',
                'maxLength'	=> 11,
            ),
        );
		$this->data     = $this->post()->safe();
    }

}
