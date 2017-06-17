<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Config;

/**
 * 新建配置信息
 * @author minggeng@meilishuo.com
 * @since 2015-04-14
 */

class ConfigCreate extends \Atom\Modules\Common\BaseModule {

    protected $data = array();

	public function run() {

        $this->_init();

        if (empty($this->data['path'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }
        /*
         * json   兼容后台
         */
        if( isset($this->data['admin_type'])&&$this->data['admin_type']==1){
            $value = base64_decode(  $this->data['value'] ) ;
            //$after_data = json_encode($after_data);
            $this->data['value'] = $value;
        }
        unset($this->data['admin_type']);
        /*
        * json
        */
        $result = Config::model()->insertOrUpdate($this->data);

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
			'path'			=> array(
                'required'	=> true,
                'type'		=> 'string',
                'maxLength'	=> 100,
			),
			'key'		=> array(
                'type'		=> 'string',
                'maxLength'	=> 50,
			),
			'value'			=> array(
                'type'		=> 'string',
			),
			'memo'			=> array(
                'type'		=> 'string',
                'maxLength'	=> 255,
			),
			'father_id'		=> array(
                'type'		=> 'integer',
                'maxLength'	=> 9,
			),
            'status'		=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
            'admin_type'		=> array(
                'type'		=> 'integer',
                'default'	=> 0,  //1为后台类型
            ),
        );
		$this->data     = $this->post()->safe();
    }

}
