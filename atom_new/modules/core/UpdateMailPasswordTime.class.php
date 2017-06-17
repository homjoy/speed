<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\MlsAccount;

/**
 * 更改用户最后一次修改密码的时间
 * @author haibinzhou@meilishuo.com
 * @since 2015-07-07
 */

class UpdateMailPasswordTime extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
	
	public function run() {
		$this->_init();

      /*  $this->params['id'] = 1;
        $this->params['update_time'] ='2015-02-01 12:12:12';
      */
        if($this->params['status']==1){
           unset($this->params['status']);
        }
        $result = MlsAccount::model()->updateDataById($this->params);

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
            'id' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'integer',
            ),
            'update_time' => array(
                'required' => TRUE,
                'allowEmpty' => FALSE,
                'type'=>'string',
            ),
            'status' => array(
                'type'=>'integer',
                'default'=>1
            ),

        );
        $this->params = $this->query()->safe();
       // $this->params['user_id'] = $this->app->currentUser['user_id'];
    }
}
