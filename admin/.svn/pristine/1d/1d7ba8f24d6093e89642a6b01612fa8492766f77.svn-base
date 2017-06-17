<?php
namespace Admin\Modules\Structure\User;
use Libs\Util\Format;
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Modules\Common\PinYin;
/**
 * @author hongzhou@meilishuo.com
 * @since 2016-04-10 下午12:53:13
 */
class AjaxAutoName extends BaseModule {

	protected $errors = NULL;
	private $params = NULL;
	public static $VIEW_SWITCH_JSON = TRUE;

	public function run() {
		$this->_init();
        $pinyin = PinYin::getInstance();
        $name_en =  $pinyin->Pinyin($this->params['name_cn'],1);

        $start  = mb_substr($this->params['name_cn'],0,1,'utf-8');
        $length = mb_strlen($this->params['name_cn']);
        $end = mb_substr($this->params['name_cn'],1,$length-1,'utf-8');
        $mail =  $pinyin->Pinyin($end.$start,1);

	    $return = Response::gen_success(
            array(
                'mail'=>$mail,
                'name_en'=>$name_en,
            )
        );
   		$this->app->response->setBody($return);

	}

	private function _init() {
		
		$this->rules = array(
			'name_cn' => array(//手机 邮箱 姓名 拼音 QQ
                'required' => TRUE,
                'allowEmpty' => FALSE,
				'type' => 'string',
			),

		);

		$this->params = $this->post()->safe();
		$this->errors = $this->post()->getErrors();
	}
}
