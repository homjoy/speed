<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Config;

/**
 * 新建配置信息
 * @author hongzhou@meilishuo.com
 * @since 2015-12-09
 */

class ConfigUpdate extends \Atom\Modules\Common\BaseModule {

    protected $params = array();

	public function run() {

        $this->_init();

        if (!isset($this->params['id'])|| !isset($this->params['path'])||empty($this->params['path'])||empty($this->params['id'])) {
        	$return = Response::gen_error(10001);
        	return $this->app->response->setBody($return);
        }
        /*
          * json   兼容后台
         */
        if( isset($this->params['admin_type'])&&$this->params['admin_type']==1){
            $value = base64_decode(  $this->params['value'] ) ;
            $this->params['value'] = $value;
        }
        unset($this->params['admin_type']);
        /*
        * json
        */
        $result = Config::model()->insertOrUpdate( $this->params );

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(50012);
        }else{
        	$return = $result;
        }

    	$this->app->response->setBody($return);
	}	

    /**
     * 参数初始化
     */
    protected function _init(){
        $data = $this->request->POST;
        $data_check = array(
            'id'			=> array(
                'required'	=> true,
                'allowEmpty'=> false,
                'type'		=> 'integer',
                'maxLength'	=> 100,
            ),
			'path'			=> array(
                'required'	=> true,
                'allowEmpty'=> false,
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
			),
            'status'		=> array(
                'type'		=> 'integer',
                'default'	=> 1,
            ),
            'admin_type'		=> array(
                'type'		=> 'integer',
                'default'	=> 0,  //1为后台类型0为自定义
            ),
        );
        $data_check     = array_intersect_key($data_check,$data);
        $this->rules    =  $data_check;
        $this->params   = $this->post()->safe();
    }

}
